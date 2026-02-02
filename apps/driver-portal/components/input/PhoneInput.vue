<script lang="ts" setup>
import PhoneInput from 'base-vue-phone-input'
import { useFocus } from '@vueuse/core'
import { ChevronsUpDown } from 'lucide-vue-next'
import FlagComponent from '../FlagComponent.vue'
import { ref } from 'vue'
import Popover from '../ui/popover/Popover.vue'
import PopoverTrigger from '../ui/popover/PopoverTrigger.vue'
import Button from '../ui/button/Button.vue'
import PopoverContent from '../ui/popover/PopoverContent.vue'
import Command from '../ui/command/Command.vue'
import CommandInput from '../ui/command/CommandInput.vue'
import CommandEmpty from '../ui/command/CommandEmpty.vue'
import CommandList from '../ui/command/CommandList.vue'
import CommandGroup from '../ui/command/CommandGroup.vue'
import CommandItem from '../ui/command/CommandItem.vue'
import Input from '../ui/input/Input.vue'

defineOptions({
  inheritAttrs: false
});

const open = ref(false);
const phoneInput = ref(null);

const countryCode = ref('CA');
const input = ref('');

const model = defineModel<{ isValid: boolean, countryCallingCode: string, nationalNumber: string }>();

const { focused } = useFocus(phoneInput);

function handleModelUpdate(e: { isValid: boolean, countryCallingCode: string, nationalNumber: string }) {
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
    class="flex shadow-blue rounded-lg"
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