import { type Ref, ref } from 'vue'
import { useIntersectionObserver } from '@vueuse/core'

export function useScrollAnimation(el: Ref<HTMLElement | null>) {
  const isVisible = ref(false)

  useIntersectionObserver(el, ([{ isIntersecting }]) => {
    if (isIntersecting) {
      isVisible.value = true
    }
  }, {
    rootMargin: '0px 0px -20px 0px'
  })

  return { isVisible }
}