# Changelog

## Unveröffentlicht

### Neu

* 🌐 **Optionale YRewrite-Domain-Zuordnung für Places:** Ist das Add-on YRewrite verfügbar, kann ein Google-Places-Eintrag im Backend einer oder mehreren YRewrite-Domains zugeordnet werden (Checkbox-Feld „Domains (YRewrite)“, nicht verpflichtend). Ohne YRewrite wird das Feld nicht angelegt — es gibt keine neue Abhängigkeit.
* ➡️ Neue `Place`-Methoden: `getDomainIds()`, `setDomainIds()`, `hasDomain()`, `findByDomainId()` und `getForDomain()` (ohne Argument wird die aktuell aufgerufene YRewrite-Domain verwendet) — praktisch für Multi-Domain-Installationen, z. B. um pro Domain den passenden Eintrag für Bewertungs-Widgets oder JSON-LD-Schema zu ermitteln.
* 💁🏻 Dokumentation des neuen Features in `docs/03_place.md`, Übersetzungen in allen sechs Sprachdateien.

### Hinweise

* Die neue Spalte `domain_ids` (Komma-Liste von Domain-IDs) wird bei der Re-Installation bzw. beim Update auf `rex_googleplaces_place_detail` angelegt. Die Abfrage-Methoden sind schema-robust: Solange die Spalte noch nicht existiert, liefern sie `null` bzw. eine leere Collection statt eines SQL-Fehlers.

## 3.0.6 und älter

Siehe [GitHub Releases](https://github.com/FriendsOfREDAXO/googleplaces/releases).
