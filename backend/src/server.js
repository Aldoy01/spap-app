import cors from "cors";
import express from "express";
import { config } from "./config.js";
import { pool } from "./db/pool.js";
import { redis } from "./cache.js";
import { osintRouter } from "./routes/osint.js";
import { reportsRouter } from "./routes/reports.js";
import { ticketsRouter } from "./routes/tickets.js";

const app = express();

app.use(cors({ origin: config.corsOrigin === "*" ? true : config.corsOrigin }));
app.use(express.json({ limit: "1mb" }));

app.get("/health", async (_req, res) => {
  const db = await pool.query("SELECT 1 AS ok").then(() => "ok").catch(() => "error");
  const cache = await redis.ping().then(() => "ok").catch(() => "error");
  res.json({ status: "ok", services: { db, cache } });
});

app.use("/api/tickets", ticketsRouter);
app.use("/api/osint", osintRouter);
app.use("/api/reports", reportsRouter);

app.use((req, res) => {
  res.status(404).json({ error: `Route not found: ${req.method} ${req.path}` });
});

app.use((error, _req, res, _next) => {
  console.error(error);
  res.status(500).json({ error: "Internal server error" });
});

app.listen(config.port, () => {
  console.log(`SPAP backend listening on port ${config.port}`);
});
