<x-mail::message>
# You've Been Invited

Click the button below to accept your invitation.

<x-mail::button :url="$url">
Accept Invitation
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
