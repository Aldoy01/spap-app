import Redis from "ioredis";
import { config } from "./config.js";

export const redis = new Redis(config.redisUrl, {
  lazyConnect: true,
  maxRetriesPerRequest: 2
});

export async function getJson(key) {
  await ensureRedis();
  const value = await redis.get(key);
  return value ? JSON.parse(value) : null;
}

export async function setJson(key, value, ttlSeconds = 60) {
  await ensureRedis();
  await redis.set(key, JSON.stringify(value), "EX", ttlSeconds);
}

export async function invalidate(pattern) {
  await ensureRedis();
  const keys = await redis.keys(pattern);
  if (keys.length) await redis.del(keys);
}

async function ensureRedis() {
  if (redis.status === "wait") {
    await redis.connect();
  }
}
