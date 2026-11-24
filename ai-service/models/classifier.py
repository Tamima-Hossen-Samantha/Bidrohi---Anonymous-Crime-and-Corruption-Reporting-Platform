from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.naive_bayes import MultinomialNB
from sklearn.pipeline import Pipeline
import numpy as np

class ReportClassifier:
    """
    Lightweight text classifier for report categorization and severity.
    Uses TF-IDF + Naive Bayes — fast, no GPU needed.
    Falls back gracefully if text is Bangla (handled by keyword matching).
    """

    # Severity keywords — both English and Bangla
    CRITICAL_KEYWORDS = [
        'child abuse', 'rape', 'murder', 'kidnap', 'trafficking',
        'শিশু নির্যাতন', 'ধর্ষণ', 'হত্যা', 'অপহরণ', 'পাচার',
        'torture', 'নির্যাতন', 'forced', 'জোরপূর্বক',
    ]

    HIGH_KEYWORDS = [
        'bribe', 'extortion', 'corruption', 'fraud', 'embezzle',
        'ঘুষ', 'চাঁদাবাজি', 'দুর্নীতি', 'প্রতারণা', 'আত্মসাৎ',
        'police', 'government', 'official', 'পুলিশ', 'সরকারি',
    ]

    MEDIUM_KEYWORDS = [
        'harassment', 'threat', 'intimidation', 'হয়রানি', 'হুমকি',
        'land grab', 'জমি দখল', 'eviction', 'উচ্ছেদ',
    ]

    def classify_severity(self, text: str) -> str:
        """Rule-based severity from keywords — works for Bangla + English."""
        text_lower = text.lower()

        for kw in self.CRITICAL_KEYWORDS:
            if kw.lower() in text_lower:
                return 'critical'

        for kw in self.HIGH_KEYWORDS:
            if kw.lower() in text_lower:
                return 'high'

        for kw in self.MEDIUM_KEYWORDS:
            if kw.lower() in text_lower:
                return 'medium'

        return 'low'

    def extract_tags(self, text: str) -> list[str]:
        """Extract relevant tags from report text."""
        tags = []
        text_lower = text.lower()

        tag_map = {
            'corruption':     ['corruption', 'bribe', 'ঘুষ', 'দুর্নীতি'],
            'child_abuse':    ['child', 'minor', 'শিশু', 'নাবালক'],
            'police':         ['police', 'পুলিশ', 'law enforcement'],
            'land':           ['land', 'জমি', 'property', 'সম্পত্তি'],
            'trafficking':    ['trafficking', 'পাচার', 'smuggling'],
            'extortion':      ['extortion', 'চাঁদাবাজি', 'ransom'],
            'government':     ['government', 'official', 'সরকার', 'কর্মকর্তা'],
            'sexual_assault': ['rape', 'sexual', 'ধর্ষণ', 'যৌন'],
        }

        for tag, keywords in tag_map.items():
            if any(kw in text_lower for kw in keywords):
                tags.append(tag)

        return tags

    def needs_ngo_alert(self, category: str, severity: str, tags: list[str]) -> bool:
        """Determine if this report warrants automatic NGO alert."""
        if severity in ['critical', 'high']:
            return True
        if category == 'child_abuse':
            return True
        if 'child_abuse' in tags or 'trafficking' in tags or 'sexual_assault' in tags:
            return True
        return False

    def recommend_ngo_type(self, category: str, tags: list[str]) -> list[str]:
        """Recommend which types of NGOs to alert."""
        types = []

        if category == 'child_abuse' or 'child_abuse' in tags:
            types.append('child_protection')
        if 'trafficking' in tags or 'sexual_assault' in tags:
            types.append('human_rights')
        if category == 'corruption' or 'corruption' in tags:
            types.append('anti_corruption')
        if not types:
            types.append('general')

        return types