from models.classifier import ReportClassifier
from typing import Dict, Any

classifier = ReportClassifier()

def analyze_report(report: Dict[str, Any]) -> Dict[str, Any]:
    """
    Full NLP analysis of a single report.
    Returns severity, tags, NGO recommendation.
    """
    text = f"{report.get('title', '')} {report.get('description', '')}"

    severity   = classifier.classify_severity(text)
    tags       = classifier.extract_tags(text)
    needs_ngo  = classifier.needs_ngo_alert(report.get('category', ''), severity, tags)
    ngo_types  = classifier.recommend_ngo_type(report.get('category', ''), tags)

    return {
        'report_id':    report.get('id'),
        'severity':     severity,
        'ai_tags':      tags,
        'needs_ngo':    needs_ngo,
        'ngo_types':    ngo_types,
        'confidence':   0.85,  # Placeholder — upgrade with transformer model
        'processed_at': __import__('datetime').datetime.now().isoformat(),
    }