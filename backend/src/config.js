import dotenv from "dotenv";

dotenv.config();

export const config = {
  env: process.env.NODE_ENV || "development",
  port: Number(process.env.PORT || 3000),
  corsOrigin: process.env.CORS_ORIGIN || "*",
  databaseUrl: process.env.DATABASE_URL || "postgres://spap:spap_password@localhost:5432/spap",
  redisUrl: process.env.REDIS_URL || "redis://localhost:6379"
};
