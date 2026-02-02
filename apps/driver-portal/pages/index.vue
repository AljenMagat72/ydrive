<script setup lang="ts">
import { definePageMeta } from '#imports';

definePageMeta({
  layoutTransition: {
    name: 'slide-up',
    mode: 'out-in'
  },
  middleware: ['auth']
});
</script>

<template>
  <main class="space-y-2 max-w-xl ml-8">
    <h1 class="font-bold text-xl">👋 Welcome to the Y DriveR Portal!</h1>
    <p>You’re now looking at the first step in what will become your all-in-one hub for everything as a Y DriveR. 
      As we continue building it out, this portal will soon give you access to:</p>

    <ul>
      <li>🚘 Ride history & records</li>
      <li>💸 Payment summaries</li>
      <li>📄 Submitting documents</li>
    </ul>

    <p class="text-underline">But right now, there's one priority:</p>

    <h2 class="font-bold text-lg">✅ Scheduling Made Simple</h2>
    <p>We’ve officially launched a new platform with a brand-new in-house scheduling tool, built by our own tech wizard, Steven.
        It’s quick, simple, and makes it much easier for dispatch to monitor coverage -we’re asking for about 5 minutes per week.</p>

    <p>It's quick, simple, and makes it much easier for dispatch to monitor coverage -we're asking for about 5 minutes
      per week.</p>

    <h2 class="font-bold text-lg">⚠️ Important Heads Up</h2>
    <p>Drivers not participating, or not meeting minimum scheduling requirements may notice changes in their experience on the platform. 
      We’re giving everyone time to adjust, but we need ALL drivers to start using it now</p>

    <h2 class="font-bold text-lg">🗓 How to Add Your Schedule</h2>
    <p>Click the "Schedule" in the top-left corner</p>

    <p>Enter your availability for this week and next using the "+" and entering     start/end times</p>

    <p>"Add Shift" will save your selection to the days assignment.</p>

    <p>You can also add additional shifts if split shift is the desired option.</p>

    <p>Thanks for being part of Y Drive and helping us improve the system as we grow.</p>
  </main>
</template>