<script setup lang="ts">
import { ref } from 'vue';
import { PinInput, PinInputGroup, PinInputSlot } from '~/components/ui/pin-input';

const props = defineProps<{ length: number }>();
const model = defineModel<string>();

const value = ref<string[]>(new Array(props.length));

function handleChange() {
  const formattedValue = value.value.join('');
  if (formattedValue.length === props.length) {
    model.value = formattedValue;
  } else {
    model.value = undefined;
  }
}

</script>

<template>
  <PinInput
    id="pin"
    v-model="value"
    otp
    class="shadow-blue rounded-md"
    @update:model-value="handleChange"
  >
    <PinInputGroup>
      <PinInputSlot
        v-for="(id, index) in length"
        :key="id"
        :index="index"
        class="text-md "
      />
    </PinInputGroup>
  </PinInput>
</template>