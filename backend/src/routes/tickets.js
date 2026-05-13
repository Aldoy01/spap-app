import { Router } from "express";
import { query } from "../db/pool.js";
import { getJson, invalidate, setJson } from "../cache.js";

export const ticketsRouter = Router();

ticketsRouter.get("/", async (req, res, next) => {
  try {
    const { type, status, region, q } = req.query;
    const cacheKey = `tickets:${type || "all"}:${status || "all"}:${region || "all"}:${q || ""}`;
    const cached = await getJson(cacheKey).catch(() => null);
    if (cached) return res.json(cached);

    const params = [];
    const where = [];

    if (type) {
      params.push(type);
      where.push(`type = $${params.length}`);
    }
    if (status) {
      params.push(status);
      where.push(`status = $${params.length}`);
    }
    if (region) {
      params.push(region);
      where.push(`region = $${params.length}`);
    }
    if (q) {
      params.push(`%${q}%`);
      where.push(`(subject ILIKE $${params.length} OR description ILIKE $${params.length} OR reporter_name ILIKE $${params.length})`);
    }

    const sql = `
      SELECT public_id, type, reporter_name, channel, region, category, priority, status,
             subject, description, assigned_unit, sla_due_at, created_at, updated_at
      FROM tickets
      ${where.length ? `WHERE ${where.join(" AND ")}` : ""}
      ORDER BY created_at DESC
      LIMIT 100
    `;

    const result = await query(sql, params);
    const payload = { data: result.rows };
    await setJson(cacheKey, payload, 30).catch(() => {});
    res.json(payload);
  } catch (error) {
    next(error);
  }
});

ticketsRouter.post("/", async (req, res, next) => {
  try {
    const ticket = req.body;
    const prefix = ticket.type === "pengaduan" ? "PEN" : "ASP";
    const sequenceResult = await query("SELECT count(*)::int + 1 AS next FROM tickets WHERE type = $1", [ticket.type]);
    const publicId = `${prefix}-2026-${String(sequenceResult.rows[0].next).padStart(3, "0")}`;

    const result = await query(
      `INSERT INTO tickets
        (public_id, type, reporter_name, reporter_contact, channel, region, category, priority, status, subject, description, assigned_unit, sla_due_at)
       VALUES
        ($1, $2, $3, $4, $5, $6, $7, $8, 'Baru', $9, $10, $11, now() + interval '2 days')
       RETURNING *`,
      [
        publicId,
        ticket.type,
        ticket.reporterName,
        ticket.reporterContact || null,
        ticket.channel || "Input Operator",
        ticket.region,
        ticket.category,
        ticket.priority || "Sedang",
        ticket.subject,
        ticket.description,
        ticket.assignedUnit || "Triage SPAP"
      ]
    );

    await query(
      "INSERT INTO ticket_events (ticket_id, event_type, note) VALUES ($1, 'created', 'Tiket dibuat dari API')",
      [result.rows[0].id]
    );
    await invalidate("tickets:*").catch(() => {});

    res.status(201).json({ data: result.rows[0] });
  } catch (error) {
    next(error);
  }
});

ticketsRouter.patch("/:publicId/status", async (req, res, next) => {
  try {
    const { status, note, actorName } = req.body;
    const result = await query(
      `UPDATE tickets
       SET status = $1,
           resolved_at = CASE WHEN $1 = 'Selesai' THEN now() ELSE resolved_at END,
           updated_at = now()
       WHERE public_id = $2
       RETURNING *`,
      [status, req.params.publicId]
    );

    if (!result.rowCount) return res.status(404).json({ error: "Ticket not found" });

    await query(
      "INSERT INTO ticket_events (ticket_id, event_type, note, actor_name) VALUES ($1, 'status_changed', $2, $3)",
      [result.rows[0].id, note || `Status diubah ke ${status}`, actorName || "Operator SPAP"]
    );
    await invalidate("tickets:*").catch(() => {});

    res.json({ data: result.rows[0] });
  } catch (error) {
    next(error);
  }
});
