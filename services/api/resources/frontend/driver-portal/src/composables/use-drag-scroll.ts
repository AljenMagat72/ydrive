import { onMounted, onUnmounted, type Ref } from 'vue';

export function useDragScroll(element: Ref<HTMLElement | null>) {
  let isDragging = false;

  function handleDown(event: PointerEvent) {
    if (!element.value) return;
    isDragging = true;

    element.value.setPointerCapture(event.pointerId);
  };

  function handleMove(event: PointerEvent) {
    if (!isDragging || !element.value) return;

    element.value.scrollLeft -= event.movementX;
    element.value.scrollTop -= event.movementY;
  };

  function handleUo(event: PointerEvent) {
    if (!element.value) return;
    isDragging = false;

    element.value.setPointerCapture(event.pointerId);
  };

  onMounted(() => {
    if (!element.value) return;
    element.value.addEventListener('pointerdown', handleDown);
    element.value.addEventListener('pointermove', handleMove);
    document.addEventListener('pointerup', handleUo);
  });

  onUnmounted(() => {
    if (!element.value) return;
    element.value.removeEventListener('pointerdown', handleDown);
    element.value.removeEventListener('pointermove', handleMove);
    document.removeEventListener('pointerup', handleUo);
  });
}
