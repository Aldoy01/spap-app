import pg from "pg";
import { config } from "../config.js";

export const pool = new pg.Pool({
  connectionString: config.databaseUrl,
  max: 10,
  idleTimeoutMillis: 30000
});

export async function query(text, params = []) {
  const startedAt = Date.now();
  const result = await pool.query(text, params);
  const duration = Date.now() - startedAt;

  if (duration > 500) {
    console.warn(`Slow query: ${duration}ms`);
  }

  return result;
}
