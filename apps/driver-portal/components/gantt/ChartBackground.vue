<template>
  <div
    class="absolute inset-0 h-full bg-striped"
    style="grid-template-columns: repeat(24, 1fr);"
  />
</template>

<style lang="css" scoped>
.bg-striped {
  background: repeating-linear-gradient(to right,
      var(--background) 0%,
      var(--background) 4.1666%,
      color-mix(in oklch, var(--background), gray 20%) 4.1666%,
      color-mix(in oklch, var(--background), gray 20%) 8.3333%);
}
</style>