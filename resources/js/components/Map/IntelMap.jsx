import { useEffect, useRef } from 'react';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const BANGLADESH = [23.6850, 90.3563];

const STATUS_COLOR = { red: '#8B0000', yellow: '#ffb800', green: '#00e676' };

export default function IntelMap({ zones, hotspots, onZoneClick }) {
    const mapRef      = useRef(null);
    const instanceRef = useRef(null);
    const layersRef   = useRef([]);

    useEffect(() => {
        if (instanceRef.current) return;

        const map = L.map(mapRef.current, {
            center:          BANGLADESH,
            zoom:            7,
            zoomControl:     true,
            attributionControl: false,
            minZoom:         6,
            maxZoom:         13,
        });

        // CartoDB dark tiles — worldmonitor-style
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            subdomains: 'abcd',
            maxZoom:    20,
        }).addTo(map);

        instanceRef.current = map;
        return () => { map.remove(); instanceRef.current = null; };
    }, []);

    // Render zones
    useEffect(() => {
        const map = instanceRef.current;
        if (!map || !zones.length) return;

        // Clear old layers
        layersRef.current.forEach(l => l.remove());
        layersRef.current = [];

        zones.forEach(zone => {
            const color  = STATUS_COLOR[zone.status] || STATUS_COLOR.green;
            const radius = 12000 + (zone.score * 300);

            const circle = L.circle([zone.latitude, zone.longitude], {
                radius,
                color:       color,
                fillColor:   color,
                fillOpacity: zone.status === 'red' ? 0.18 : zone.status === 'yellow' ? 0.10 : 0.05,
                weight:      zone.status === 'red' ? 1.5 : 1,
                opacity:     zone.status === 'green' ? 0.3 : 0.7,
            }).addTo(map);

            // Dot at center
            const dot = L.circleMarker([zone.latitude, zone.longitude], {
                radius:      zone.status === 'red' ? 4 : 3,
                color:       color,
                fillColor:   color,
                fillOpacity: 1,
                weight:      0,
            }).addTo(map);

            // Tooltip — worldmonitor style
            const tooltip = `
                <div style="
                    background: rgba(8,8,8,0.95);
                    border: 1px solid rgba(255,255,255,0.1);
                    border-left: 2px solid ${color};
                    padding: 8px 12px;
                    font-family: 'IBM Plex Mono', monospace;
                    font-size: 11px;
                    color: rgba(255,255,255,0.9);
                    min-width: 160px;
                    border-radius: 2px;
                ">
                    <div style="font-size:13px;font-family:'Noto Sans Bengali';color:${color};font-weight:600;margin-bottom:4px">
                        ${zone.name_bn}
                    </div>
                    <div style="color:rgba(255,255,255,0.4);font-size:10px;margin-bottom:6px;letter-spacing:1px">
                        ${zone.name.toUpperCase()}
                    </div>
                    <div style="display:flex;gap:16px">
                        <span>SCORE <span style="color:${color};font-weight:600">${zone.score}</span></span>
                        <span>RPT <span style="color:rgba(255,255,255,0.7)">${zone.report_count}</span></span>
                    </div>
                </div>
            `;

            dot.bindTooltip(tooltip, {
                permanent:  false,
                direction:  'top',
                offset:     [0, -6],
                opacity:    1,
                className:  'intel-tooltip',
            });

            dot.on('click', () => onZoneClick && onZoneClick(zone));

            layersRef.current.push(circle, dot);
        });

        // Hotspots
        hotspots.forEach(h => {
            const ring = L.circle([h.latitude, h.longitude], {
                radius:      h.radius_meters || 2000,
                color:       '#ff2d2d',
                fillColor:   '#ff2d2d',
                fillOpacity: (h.intensity / 100) * 0.35,
                weight:      1,
                opacity:     0.6,
                dashArray:   '4 4',
            }).addTo(map);
            layersRef.current.push(ring);
        });

    }, [zones, hotspots]);

    return (
        <div
            ref={mapRef}
            style={{ width: '100%', height: '100%', background: '#080808' }}
        />
    );
}