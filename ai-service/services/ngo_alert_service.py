import httpx
import os
from typing import Dict, Any

LARAVEL_URL   = os.getenv("LARAVEL_API_URL", "http://127.0.0.1:8000/api")
ADMIN_TOKEN   = os.getenv("LARAVEL_ADMIN_TOKEN", "")

# NGO registry — in production, pull from DB via Laravel API
NGO_REGISTRY = {
    'child_protection': [
        {'name': 'Bangladesh Shishu Adhikar Forum', 'email': 'alert@bsaf.org.bd'},
        {'name': 'UNICEF Bangladesh',               'email': 'alert@unicef.bd'},
    ],
    'anti_corruption': [
        {'name': 'Transparency International BD',  'email': 'alert@ti-bangladesh.org'},
        {'name': 'Sujan Bangladesh',               'email': 'alert@sujanbd.org'},
    ],
    'human_rights': [
        {'name': 'Ain o Salish Kendra',            'email': 'alert@askbd.org'},
        {'name': 'Odhikar',                        'email': 'alert@odhikar.org'},
    ],
    'general': [
        {'name': 'BRAC',                           'email': 'alert@brac.net'},
    ],
}

async def send_ngo_alerts(report_id: int, ngo_types: list[str], analysis: Dict[str, Any]) -> list[Dict]:
    """
    Send alerts to relevant NGOs and notify Laravel to record them.
    """
    alerts_sent = []

    for ngo_type in ngo_types:
        ngos = NGO_REGISTRY.get(ngo_type, [])

        for ngo in ngos:
            # In production: send real email via SMTP/SendGrid
            # For now: POST to Laravel to record the alert
            alert_data = {
                'report_id':  report_id,
                'ngo_name':   ngo['name'],
                'ngo_email':  ngo['email'],
                'ngo_type':   ngo_type,
                'alert_type': 'auto',
                'message':    build_alert_message(report_id, analysis),
                'status':     'sent',
            }

            try:
                async with httpx.AsyncClient() as client:
                    response = await client.post(
                        f"{LARAVEL_URL}/internal/ngo-alerts",
                        json=alert_data,
                        headers={
                            'Authorization': f'Bearer {ADMIN_TOKEN}',
                            'Accept':        'application/json',
                        },
                        timeout=10.0,
                    )
                    if response.status_code in [200, 201]:
                        alerts_sent.append(alert_data)

            except httpx.RequestError as e:
                print(f"Failed to notify Laravel for NGO {ngo['name']}: {e}")

    return alerts_sent


def build_alert_message(report_id: int, analysis: Dict) -> str:
    return (
        f"BIDROHI ALERT — Report #{report_id}\n"
        f"Severity: {analysis.get('severity', 'unknown').upper()}\n"
        f"Tags: {', '.join(analysis.get('ai_tags', []))}\n"
        f"This report was automatically flagged by the BIDROHI AI system.\n"
        f"Please review urgently."
    )