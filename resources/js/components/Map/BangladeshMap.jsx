import { GoogleMap, useJsApiLoader, Circle, Marker, InfoWindow } from '@react-google-maps/api';
import { useState, useCallback } from 'react';

const BANGLADESH_CENTER = { lat: 23.6850, lng: 90.3563 };

const STATUS_COLORS = {
    red:    { fill: '#e63946', stroke: '#e63946' },
    yellow: { fill: '#d29922', stroke: '#d29922' },
    green:  { fill: '#3fb950', stroke: '#3fb950' },
};

const MAP_STYLES = [
    { elementType: 'geometry',            stylers: [{ color: '#0d1117' }] },
    { elementType: 'labels.text.fill',    stylers: [{ color: '#7d8590' }] },
    { elementType: 'labels.text.stroke',  stylers: [{ color: '#0d1117' }] },
    { featureType: 'water',               elementType: 'geometry', stylers: [{ color: '#080c10' }] },
    { featureType: 'road',                elementType: 'geometry', stylers: [{ color: '#161b22' }] },
    { featureType: 'road',                elementType: 'geometry.stroke', stylers: [{ color: '#21262d' }] },
    { featureType: 'administrative',      elementType: 'geometry.stroke', stylers: [{ color: '#30363d' }] },
    { featureType: 'poi',                 stylers: [{ visibility: 'off' }] },
    { featureType: 'transit',             stylers: [{ visibility: 'off' }] },
    { featureType: 'landscape',           elementType: 'geometry', stylers: [{ color: '#0d1117' }] },
    { featureType: 'administrative.country', elementType: 'geometry.stroke', stylers: [{ color: '#e63946' }, { weight: 1.5 }] },
];

export default function BangladeshMap({ zones, hotspots, onZoneClick }) {
    const [selected, setSelected] = useState(null);

    const { isLoaded } = useJsApiLoader({
        googleMapsApiKey: import.meta.env.VITE_GOOGLE_MAPS_KEY || '',
    });

    const onLoad = useCallback(map => {
        // Restrict to Bangladesh bounds
        const bounds = new window.google.maps.LatLngBounds(
            { lat: 20.3, lng: 87.9 },
            { lat: 26.7, lng: 92.7 }
        );
        map.fitBounds(bounds);
    }, []);

    if (!isLoaded) return (
        <div style={{
            width: '100%', height: '100%',
            display: 'flex', alignItems: 'center', justifyContent: 'center',
            background: 'var(--dark)',
            fontFamily: 'var(--font-mono)',
            color: 'var(--text-muted)',
            fontSize: '13px',
            letterSpacing: '2px',
        }}>
            LOADING INTELLIGENCE MAP...
        </div>
    );

    return (
        <GoogleMap
            mapContainerStyle={{ width: '100%', height: '100%' }}
            center={BANGLADESH_CENTER}
            zoom={7}
            onLoad={onLoad}
            options={{
                styles:              MAP_STYLES,
                disableDefaultUI:    true,
                zoomControl:         true,
                gestureHandling:     'greedy',
                minZoom:             6,
                maxZoom:             14,
                backgroundColor:     '#080c10',
            }}
        >
            {/* Zone circles */}
            {zones.map(zone => {
                const colors = STATUS_COLORS[zone.status] || STATUS_COLORS.green;
                const radius = 15000 + (zone.score * 200); // bigger = more dangerous

                return (
                    <Circle
                        key={zone.id}
                        center={{ lat: zone.latitude, lng: zone.longitude }}
                        radius={radius}
                        options={{
                            fillColor:    colors.fill,
                            fillOpacity:  zone.status === 'red' ? 0.25 : zone.status === 'yellow' ? 0.15 : 0.08,
                            strokeColor:  colors.stroke,
                            strokeOpacity:zone.status === 'green' ? 0.2 : 0.6,
                            strokeWeight: zone.status === 'red' ? 2 : 1,
                        }}
                        onClick={() => {
                            setSelected(zone);
                            onZoneClick && onZoneClick(zone);
                        }}
                    />
                );
            })}

            {/* Hotspot markers */}
            {hotspots.map(hotspot => (
                <Circle
                    key={`hotspot-${hotspot.id}`}
                    center={{ lat: hotspot.latitude, lng: hotspot.longitude }}
                    radius={hotspot.radius_meters || 2000}
                    options={{
                        fillColor:    '#e63946',
                        fillOpacity:  (hotspot.intensity / 100) * 0.5,
                        strokeColor:  '#e63946',
                        strokeOpacity:0.8,
                        strokeWeight: 1,
                    }}
                />
            ))}

            {/* Selected zone info window */}
            {selected && (
                <InfoWindow
                    position={{ lat: selected.latitude, lng: selected.longitude }}
                    onCloseClick={() => setSelected(null)}
                    options={{ pixelOffset: new window.google.maps.Size(0, -10) }}
                >
                    <div style={{
                        background:  '#0d1117',
                        border:      '1px solid #30363d',
                        borderRadius:'6px',
                        padding:     '12px 16px',
                        minWidth:    '180px',
                        fontFamily:  'Rajdhani, sans-serif',
                    }}>
                        <div style={{ color: '#e63946', fontWeight: 700, fontSize: '15px' }}>
                            {selected.name_bn}
                        </div>
                        <div style={{ color: '#7d8590', fontSize: '12px', marginBottom: '8px' }}>
                            {selected.name}
                        </div>
                        <div style={{ display: 'flex', gap: '12px', fontSize: '12px' }}>
                            <span style={{ color: '#e6edf3' }}>
                                স্কোর: <strong style={{ color: selected.status === 'red' ? '#e63946' : selected.status === 'yellow' ? '#d29922' : '#3fb950' }}>
                                    {selected.score}
                                </strong>
                            </span>
                            <span style={{ color: '#e6edf3' }}>
                                রিপোর্ট: <strong>{selected.report_count}</strong>
                            </span>
                        </div>
                    </div>
                </InfoWindow>
            )}
        </GoogleMap>
    );
}