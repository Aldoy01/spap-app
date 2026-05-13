import { readFile } from "node:fs/promises";
import { fileURLToPath } from "node:url";
import { dirname, join } from "node:path";
import { pool } from "./pool.js";

const __dirname = dirname(fileURLToPath(import.meta.url));
const schemaPath = join(__dirname, "schema.postgres.sql");

async function migrate() {
  const schema = await readFile(schemaPath, "utf8");
  await pool.query(schema);
  console.log("Database migration completed");
  await pool.end();
}

migrate().catch(async error => {
  console.error(error);
  await pool.end();
  process.exit(1);
});
