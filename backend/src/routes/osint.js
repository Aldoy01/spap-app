import { Router } from "express";
import { query } from "../db/pool.js";
import { getJson, setJson } from "../cache.js";

export const osintRouter = Router();

osintRouter.get("/mentions", async (_req, res, next) => {
  try {
    const cached = await getJson("osint:mentions").catch(() => null);
    if (cached) return res.json(cached);

    const result = await query(
      `SELECT source, keyword, cluster_name, sentiment, mention_count, sample_text, recommendation, captured_at
       FROM osint_mentions
       ORDER BY captured_at DESC, mention_count DESC
       LIMIT 50`
    );

    const payload = { data: result.rows };
    await setJson("osint:mentions", payload, 60).catch(() => {});
    res.json(payload);
  } catch (error) {
    next(error);
  }
});
