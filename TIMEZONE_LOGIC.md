# Gestion des Timezones - Architecture

## 🎯 Principe de base

**TOUT est stocké en UTC dans la base de données** + la timezone de l'utilisateur pour l'affichage.

## 📊 Structure de la BDD

```sql
event:
  - startDateTime: datetime (UTC)
  - timezone: varchar (ex: 'Europe/Paris', 'America/New_York')
  - duration: int (minutes)
```

## 🔄 Flux de données

### 1️⃣ Création d'un événement (Appli → Google → BDD)

```
User input: 2025-10-25 14:00 (Europe/Paris)
     ↓
Conversion en UTC: 2025-10-25 12:00 (UTC)
     ↓
Envoi à Google Calendar avec timezone
     ↓
Webhook Google → API
     ↓
Stockage BDD:
  - startDateTime: '2025-10-25 12:00:00' (UTC)
  - timezone: 'Europe/Paris'
```

**Fichier**: `ControllerCalendar::createEvent()`
```php
$userStartDateTimeUTC = $userStartDateTime->setTimezone(new DateTimeZone('UTC'));
$userStartDateTimeUTCToString = $userStartDateTimeUTC->format('Y-m-d H:i:s');
// Stocké en UTC, timezone utilisateur préservée
```

### 2️⃣ Webhook Google → BDD

**Fichier**: `ControllerGoogle::updateCalendar()`
```php
// Google envoie ISO8601
$dtStart = new DateTime($startDateTimeISO);
$dtStartUTC = (clone $dtStart)->setTimezone(new DateTimeZone('UTC'));
$startDateTimeUTC = $dtStartUTC->format('Y-m-d H:i:s');
$eventTimezone = $eventStart->getTimeZone() ?? 'Europe/Paris';
// Stocké en UTC avec timezone de l'événement
```

### 3️⃣ Affichage (BDD → Frontend)

**Backend**: Retourne directement UTC + timezone
```php
[
  'startDateTime' => '2025-10-25 12:00:00',  // UTC
  'timezone' => 'Europe/Paris'
]
```

**Frontend**: `TableUser.tsx`
```typescript
// Convertit UTC → timezone de l'événement
const utcDate = new Date(`${year}-${month}-${day}T${hour}:${minute}:${second}Z`);
const formattedTime = new Intl.DateTimeFormat('fr-FR', {
    hour: '2-digit',
    minute: '2-digit',
    timeZone: timezone,  // 'Europe/Paris'
    hour12: false
}).format(utcDate);
// Affiche: 14:00 (heure locale Paris)
```

## ✅ Avantages

1. **Cohérence**: Une seule source de vérité (UTC)
2. **Flexibilité**: Support de n'importe quel timezone
3. **Calculs simples**: Comparaisons sans bug DST
4. **Multizone**: Utilisateurs dans différents pays
5. **Évite doublons**: Même ID événement = même heure UTC

## 🔍 Cas d'usage

### Utilisateur à Paris crée un RDV
```
Input: 25/10/2025 14:00 (Europe/Paris)
BDD: 2025-10-25 12:00:00 UTC + timezone='Europe/Paris'
Affichage Paris: 14:00
Affichage New York: 08:00 (si besoin)
```

### Utilisateur à New York crée un RDV
```
Input: 25/10/2025 14:00 (America/New_York)
BDD: 2025-10-25 18:00:00 UTC + timezone='America/New_York'
Affichage NY: 14:00
Affichage Paris: 20:00 (si besoin)
```

## 🛠️ Fichiers modifiés

- ✅ `ControllerCalendar::createEvent()` - Stocke en UTC
- ✅ `ControllerGoogle::updateCalendar()` - Convertit événements Google en UTC
- ✅ `ControllerGoogle::createNewEventFromGoogle()` - Crée avec UTC + timezone
- ✅ `ControllerGoogle::updateExistingEvent()` - Update avec UTC + timezone
- ✅ `TableUser.tsx::formatDateTime()` - Convertit UTC → timezone affichage

## 🚨 Important

- **Toujours** stocker en UTC dans la BDD
- **Toujours** garder la timezone de l'utilisateur
- **Jamais** faire de calculs sur des dates locales
- **Toujours** convertir au dernier moment (affichage)
