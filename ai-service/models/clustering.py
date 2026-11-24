import numpy as np
from sklearn.cluster import KMeans, DBSCAN
from sklearn.preprocessing import StandardScaler
from typing import List, Dict, Any

class HotspotClusterer:
    """
    Geospatial clustering to detect crime hotspots.
    Uses DBSCAN — better than k-means for geographic data
    because it finds arbitrary-shaped clusters and handles noise.
    """

    def __init__(self, eps_km: float = 2.0, min_samples: int = 3):
        # eps in degrees (~2km at Bangladesh's latitude)
        self.eps        = eps_km / 111.0
        self.min_samples = min_samples

    def detect_hotspots(self, reports: List[Dict]) -> List[Dict]:
        """
        Takes a list of reports with lat/lng.
        Returns list of detected hotspot clusters.
        """
        if len(reports) < self.min_samples:
            return []

        coords = np.array([
            [r['latitude'], r['longitude']] for r in reports
            if r.get('latitude') and r.get('longitude')
        ])

        if len(coords) < self.min_samples:
            return []

        # Run DBSCAN
        db = DBSCAN(eps=self.eps, min_samples=self.min_samples, metric='euclidean')
        labels = db.fit_predict(coords)

        hotspots = []
        unique_labels = set(labels)

        for label in unique_labels:
            if label == -1:
                continue  # Noise points — not a cluster

            # Get all points in this cluster
            mask         = labels == label
            cluster_pts  = coords[mask]
            cluster_reps = [r for i, r in enumerate(reports) if mask[i]]

            centroid  = cluster_pts.mean(axis=0)
            intensity = self._intensity(cluster_reps)
            category  = self._dominant_category(cluster_reps)
            radius    = self._estimate_radius(cluster_pts, centroid)

            hotspots.append({
                'cluster_id':   int(label),
                'latitude':     round(float(centroid[0]), 6),
                'longitude':    round(float(centroid[1]), 6),
                'report_count': len(cluster_reps),
                'intensity':    intensity,
                'category':     category,
                'radius_meters':radius,
                'report_ids':   [r['id'] for r in cluster_reps],
            })

        # Sort by intensity descending
        return sorted(hotspots, key=lambda x: x['intensity'], reverse=True)

    def _intensity(self, reports: List[Dict]) -> int:
        """0–100 intensity score."""
        count = len(reports)
        base  = min(60, count * 12)

        # Recency boost
        from datetime import datetime, timedelta
        recent = sum(
            1 for r in reports
            if r.get('created_at') and
            (datetime.now() - datetime.fromisoformat(str(r['created_at']))).days <= 7
        )
        boost = min(40, recent * 15)

        return min(100, base + boost)

    def _dominant_category(self, reports: List[Dict]) -> str:
        categories = [r.get('category', 'other') for r in reports]
        return max(set(categories), key=categories.count)

    def _estimate_radius(self, points: np.ndarray, centroid: np.ndarray) -> int:
        """Estimate cluster radius in meters."""
        distances = np.sqrt(((points - centroid) ** 2).sum(axis=1))
        max_dist_deg = float(distances.max()) if len(distances) > 0 else 0.02
        return min(5000, int(max_dist_deg * 111000))