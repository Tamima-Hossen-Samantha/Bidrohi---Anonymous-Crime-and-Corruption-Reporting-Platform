from fastapi import APIRouter, HTTPException, Header
from pydantic import BaseModel
from typing import List, Optional, Any, Dict
from services.nlp_service import analyze_report
from services.cluster_service import detect_hotspots
from services.ngo_alert_service import send_ngo_alerts
import os

router = APIRouter()

SECRET_KEY = os.getenv("SECRET_KEY", "bidrohi_ai_secret_2026")

def verify_secret(x_secret_key: str = Header(...)):
    if x_secret_key != SECRET_KEY:
        raise HTTPException(status_code=403, detail="Invalid secret key.")

# ── Schemas ───────────────────────────────────────────────────────

class ReportInput(BaseModel):
    id:          int
    title:       str
    description: str
    category:    str
    district:    Optional[str] = None
    latitude:    Optional[float] = None
    longitude:   Optional[float] = None
    created_at:  Optional[str] = None

class AnalyzeRequest(BaseModel):
    report: ReportInput

class ClusterRequest(BaseModel):
    reports:  List[ReportInput]
    zone_id:  int

# ── Endpoints ─────────────────────────────────────────────────────

@router.post("/analyze")
async def analyze(payload: AnalyzeRequest, x_secret_key: str = Header(...)):
    """
    Analyze a single report — classify severity, extract tags,
    determine NGO alert need.
    Called by Laravel after every new report submission.
    """
    verify_secret(x_secret_key)

    report   = payload.report.model_dump()
    analysis = analyze_report(report)

    # Auto-send NGO alerts if needed
    alerts = []
    if analysis['needs_ngo']:
        alerts = await send_ngo_alerts(
            report['id'],
            analysis['ngo_types'],
            analysis,
        )

    return {
        'success':  True,
        'analysis': analysis,
        'alerts':   alerts,
    }


@router.post("/cluster")
async def cluster(payload: ClusterRequest, x_secret_key: str = Header(...)):
    """
    Run DBSCAN clustering on reports from a zone.
    Returns detected hotspots.
    Called by Laravel on a schedule (every hour).
    """
    verify_secret(x_secret_key)

    reports   = [r.model_dump() for r in payload.reports]
    hotspots  = detect_hotspots(reports)

    return {
        'success':   True,
        'zone_id':   payload.zone_id,
        'hotspots':  hotspots,
        'count':     len(hotspots),
    }


@router.post("/batch-analyze")
async def batch_analyze(reports: List[ReportInput], x_secret_key: str = Header(...)):
    """
    Analyze multiple unprocessed reports at once.
    Called by Laravel's scheduled command.
    """
    verify_secret(x_secret_key)

    results = []
    for report in reports:
        analysis = analyze_report(report.model_dump())
        results.append(analysis)

    return {
        'success': True,
        'count':   len(results),
        'results': results,
    }