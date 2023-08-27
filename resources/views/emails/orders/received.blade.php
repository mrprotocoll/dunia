<x-mail::message>
# Introduction

Order received

<x-mail::button :url="''" color="success">
Button Text
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
