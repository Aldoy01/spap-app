import { Router } from "express";
import { query } from "../db/pool.js";

export const reportsRouter = Router();

reportsRouter.get("/summary", async (req, res, next) => {
  try {
    const region = req.query.region;
    const params = [];
    const where = [];

    if (region) {
      params.push(region);
      where.push(`region = $${params.length}`);
    }

    const whereClause = where.length ? `WHERE ${where.join(" AND ")}` : "";
    const [statusResult, categoryResult, osintResult] = await Promise.all([
      query(`SELECT status, count(*)::int AS total FROM tickets ${whereClause} GROUP BY status`, params),
      query(`SELECT category, count(*)::int AS total FROM tickets ${whereClause} GROUP BY category ORDER BY total DESC`, params),
      query("SELECT keyword, sentiment, mention_count FROM osint_mentions ORDER BY mention_count DESC LIMIT 5")
    ]);

    res.json({
      data: {
        status: statusResult.rows,
        categories: categoryResult.rows,
        osint: osintResult.rows
      }
    });
  } catch (error) {
    next(error);
  }
});

reportsRouter.post("/jobs", async (req, res, next) => {
  try {
    const { reportType, period, region, outputFormat, payload = {} } = req.body;
    const result = await query(
      `INSERT INTO report_jobs (report_type, period, region, output_format, payload)
       VALUES ($1, $2, $3, $4, $5)
       RETURNING *`,
      [reportType, period, region, outputFormat, payload]
    );
    res.status(201).json({ data: result.rows[0] });
  } catch (error) {
    next(error);
  }
});
