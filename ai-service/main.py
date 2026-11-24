from fastapi import FastAPI, HTTPException, Header
from fastapi.middleware.cors import CORSMiddleware
from contextlib import asynccontextmanager
from routers import analysis
import os
from dotenv import load_dotenv

load_dotenv()

@asynccontextmanager
async def lifespan(app: FastAPI):
    print("🤖 BIDROHI AI Microservice starting...")
    print("📡 Connected to Laravel at:", os.getenv("LARAVEL_API_URL"))
    yield
    print("🛑 BIDROHI AI Microservice shutting down.")

app = FastAPI(
    title="BIDROHI AI Microservice",
    description="NLP classification + k-means clustering + NGO alert engine",
    version="1.0.0",
    lifespan=lifespan,
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["http://localhost:8000", "http://127.0.0.1:8000"],
    allow_methods=["*"],
    allow_headers=["*"],
)

app.include_router(analysis.router, prefix="/api")

@app.get("/")
def root():
    return {
        "service": "BIDROHI AI Microservice",
        "status":  "running",
        "version": "1.0.0",
    }

@app.get("/health")
def health():
    return {"status": "ok"}