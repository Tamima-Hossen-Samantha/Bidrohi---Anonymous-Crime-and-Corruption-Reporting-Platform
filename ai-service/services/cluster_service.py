from models.clustering import HotspotClusterer
from typing import List, Dict, Any

clusterer = HotspotClusterer(eps_km=2.0, min_samples=3)

def detect_hotspots(reports: List[Dict[str, Any]]) -> List[Dict[str, Any]]:
    """Run DBSCAN hotspot detection on a list of reports."""
    return clusterer.detect_hotspots(reports)