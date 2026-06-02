<script lang="ts" setup>
import { ref } from 'vue'
import PhoneInput from 'base-vue-phone-input'
import { useFocus } from '@vueuse/core'
import { ChevronsUpDown } from 'lucide-vue-next'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Popover, PopoverTrigger, PopoverContent } from '@/components/ui/popover'
import { Command, CommandInput, CommandEmpty, CommandList, CommandGroup, CommandItem } from '@/components/ui/command'

import FlagComponent from '@/components/FlagComponent.vue';

export type PhoneInputModel = { isValid: boolean, countryCallingCode: string, nationalNumber: string };

defineOptions({
  inheritAttrs: false
});

const open = ref(false);
const phoneInput = ref(null);

const countryCode = ref('CA');
const input = ref('');

const model = defineModel<PhoneInputModel>();

const { focused } = useFocus(phoneInput);

function handleModelUpdate(e: PhoneInputModel) {
  model.value = e;
}

</script>

<template>
  <PhoneInput
    v-model="input"
    v-model:country-code="countryCode"
    :no-formatting-as-you-type="true"
    :no-use-browser-locale="true"
    :auto-format="false"
    placeholder=" "
    class="flex"
    @update="handleModelUpdate"
  >
    <template #selector="{ inputValue, updateInputValue, countries }">
      <Popover v-model:open="open">
        <PopoverTrigger>
          <Button
            type="button"
            variant="outline"
            class="flex gap-1 rounded-e-none rounded-s-lg px-3"
          >
            <FlagComponent :country="inputValue" />
            <ChevronsUpDown class="-mr-2 h-4 w-4 opacity-50" />
          </Button>
        </PopoverTrigger>
        <PopoverContent class="w-[300px] p-0">
          <Command>
            <CommandInput placeholder="Search country..." />
            <CommandEmpty>No country found.</CommandEmpty>
            <CommandList>
              <CommandGroup>
                <CommandItem
                  v-for="option in countries"
                  :key="option.iso2"
                  :value="option.name"
                  class="gap-2"
                  @select="
                    () => {
                      updateInputValue(option.iso2)
                      open = false
                      focused = true
                    }
                  "
                >
                  <FlagComponent :country="option?.iso2" />
                  <span class="flex-1 text-sm">
                    {{ option.name }}</span>
                  <span class="text-foreground/50 text-sm">
                    {{ option.dialCode }}
                  </span>
                </CommandItem>
              </CommandGroup>
            </CommandList>
          </Command>
        </PopoverContent>
      </Popover>
    </template>

    <template #input="{ inputValue, updateInputValue, placeholder }">
      <Input
        v-bind="$attrs"
        ref="phoneInput"
        class="rounded-e-lg rounded-s-none"
        type="text"
        :model-value="inputValue"
        :placeholder="placeholder"
        @input="updateInputValue"
      />
    </template>
  </PhoneInput>
</template>