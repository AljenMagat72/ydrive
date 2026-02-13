<template>
  <div class="bg-black border border-gray-800 rounded-2xl p-6 flex flex-col gap-4 max-w-md shadow-lg">
    <div class="flex justify-between items-center">
      <div class="flex items-center gap-2 text-white">
        <div class="bg-blue-600/20 p-1.5 rounded-lg">
          <Shield class="w-5 h-5 text-blue-500" />
        </div>
        <span class="text-xl font-semibold tracking-tight">License</span>
      </div>
      <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-sm font-medium border border-green-500/30">
        Valid
      </span>
    </div>

    <div class="flex gap-6 mt-2">
      <div class="w-48 h-32 border-2 border-dashed border-gray-700 rounded-xl flex items-center justify-center relative bg-gray-900/50 group overflow-hidden">
        <svg class="w-16 h-16 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
      </div>

      <div class="flex flex-col justify-center space-y-2">
        <h3 class="text-white text-lg font-medium leading-tight">Intact Insurance</h3>
        <div class="space-y-1">
          <p class="text-gray-400 text-sm flex gap-2">
            <span class="font-semibold text-white">Policy:</span> P12JFAS141
          </p>
          <p class="text-gray-400 text-sm flex gap-2">
            <span class="font-semibold text-white">Expires:</span> Jun 12, 2026
          </p>
        </div>
        
        <button class="mt-2 bg-white text-black font-semibold py-2 px-6 rounded-full hover:bg-gray-200 transition-colors">
          Upload New
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Shield } from 'lucide-vue-next';
</script>