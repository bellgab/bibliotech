@component('mail::message')
# Könyv visszahozási emlékeztető

Kedves {{ $user->name }}!

@if($isOverdue)
⚠️ **FIGYELEM:** A kölcsönzött könyv visszahozási határideje **lejárt**!
@else
🔔 Emlékeztetjük, hogy a kölcsönzött könyv visszahozási határideje közeledik.
@endif

## Könyv adatai
**Cím:** {{ $book->title }}  
**Szerző:** {{ $book->author->name }}  
**Kategória:** {{ $book->category->name }}

## Kölcsönzési információk
**Visszahozási határidő:** {{ $dueDate->format('Y. m. d.') }}  
@if($isOverdue)
**Késés:** {{ now()->diffInDays($dueDate) }} nap
@else
**Hátralévő idő:** {{ now()->diffInDays($dueDate) }} nap
@endif

@if($isOverdue)
@component('mail::panel')
**KÉSEDELMI DÍJ**

A könyv lejárt visszahozása miatt késedelmi díj számítható fel. Kérjük, hozza vissza a könyvet a lehető leghamarabb!
@endcomponent
@endif

@component('mail::button', ['url' => route('books.show', $book)])
Könyv megtekintése
@endcomponent

@if($isOverdue)
@component('mail::button', ['url' => route('dashboard'), 'color' => 'red'])
Saját kölcsönzések
@endcomponent
@endif

---

**Elérhetőségeink:**
- 📞 Telefon: +36 1 234 5678
- 📧 Email: info@bibliotech.hu
- 🌐 Weboldal: {{ config('app.url') }}

Köszönjük együttműködését!

**{{ config('app.name') }} csapata**
@endcomponent
