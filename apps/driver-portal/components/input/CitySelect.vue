<script setup lang="ts">
import { 
  Select, 
  SelectTrigger, 
  SelectValue, 
  SelectContent, 
  SelectGroup, 
  SelectItem 
} from '../ui/select';
</script>

<template>
  <Select
    default-value="All"
  >
    <SelectTrigger
      v-bind=$attrs
      class="w-26"
    >
      <SelectValue />
    </SelectTrigger>
    <SelectContent>
      <SelectGroup>
        <SelectItem value="All">All</SelectItem>
        <SelectItem value="Peterborough">Peterborough</SelectItem>
        <SelectItem value="Huntsville">Huntsville</SelectItem>
        <SelectItem value="Lindsay">Lindsay</SelectItem>
        <SelectItem value="Port Hope / Cobourg">Port Hope / Cobourg</SelectItem>
        <SelectItem value="Grande Prairie">Grande Prairie</SelectItem>
        <SelectItem value="Medicine Hat">Medicine Hat</SelectItem>
        <SelectItem value="Lethbridge">Lethbridge</SelectItem>
      </SelectGroup>
    </SelectContent>
  </Select>
</template>