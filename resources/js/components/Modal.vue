<template>
    <div
      v-if="isVisible"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[999]"
    > <div
        ref="modalContent"
        class="bg-white p-6 rounded-lg shadow-lg w-full"
        :class="modalClasses"
      >
        <div class="flex justify-between mb-4">
          <h2 class="text-2xl font-semibold">{{ title }}</h2>
          <i class="fas fa-x cursor-pointer" @click="closeModal" />
        </div>
        <slot></slot>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, computed, onMounted, onBeforeUnmount, nextTick } from "vue";
  
  interface modalProps {
    title: string;
    backDrop?: boolean;
    scrollable?: boolean;
    size?:
      | "sm"
      | "md"
      | "lg"
      | "xl"
      | "2xl"
      | "3xl"
  }
  
  const props = withDefaults(defineProps<modalProps>(), {
    backDrop: true,
    scrollable: false,
    size: "lg",
  });
  
  const emits = defineEmits<{
    (event: "open"): void;
    (event: "close"): void;
  }>();
  
  const isVisible = ref(false);
  const modalContent = ref<any>(null);
  const isOverflowing = ref(false);
  const clickTarget = ref(null);
  // const modalWrapper = ref(null);
  
  const modalClasses = computed(() => ({
    "max-w-sm": props.size === "sm",
    "max-w-md": props.size === "md",
    "max-w-lg": props.size === "lg",
    "max-w-xl": props.size === "xl",
    "max-w-2xl": props.size === "2xl",
    "max-w-3xl": props.size === "3xl",
    "max-h-[80vh] overflow-y-auto": props.scrollable === true,
    "overflow-y-auto": isOverflowing,
    "overflow-y-hidden": !isOverflowing,
  }));
  
  const checkOverflow = () => {
    nextTick(() => {
      if (modalContent.value) {
        isOverflowing.value =
          modalContent.value.scrollHeight > modalContent.value.clientHeight;
      }
    });
  };
  
  onMounted(() => {
    checkOverflow();
    window.addEventListener("resize", checkOverflow);
    document.body.addEventListener("mousedown", onMouseDown);
    document.body.addEventListener("mouseup", onMouseUp);
  });
  
  onBeforeUnmount(() => {
    window.removeEventListener("resize", checkOverflow);
    document.body.removeEventListener("mousedown", onMouseDown);
    document.body.removeEventListener("mouseup", onMouseUp);
  });
  
  const openModal = () => {
    isVisible.value = true;
    emits("open");
  };
  
  const closeModal = () => {
    isVisible.value = false;
    emits("close");
  };
  
  const onMouseDown = (event) => {
    if (!modalContent.value) return;
    clickTarget.value = event.target;
  };
  
  const onMouseUp = (event) => {
    if (!modalContent.value) return;
    const clickedDownOutside = !modalContent.value.contains(clickTarget.value);
    const clickedUpOutside = !modalContent.value.contains(event.target);
  
    if (isVisible.value && clickedDownOutside && clickedUpOutside) {
      closeModal();
    }
  };
  
  const backDropCloseModal = () => {
    if (props.backDrop) isVisible.value = false;
    emits("close");
  };
  
  defineExpose({
    openModal,
    closeModal,
  });
  </script>
  