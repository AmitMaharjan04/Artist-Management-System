<template>
  <div
    class="mb-5 overflow-x-auto"
    :class="{
      tabs: !fullWidth,
    }"
  >
    <ul class="c-tab-grid border-b-2 border-gray-200">
      <li
        v-for="(tab, index) in tabs"
        :key="index"
        class="pb-1 px-3 cursor-pointer c-tab-list text-center text-base"
        :class="
          show === tab.id
            ? 'c-tab-active color-red font-bold'
            : 'c-tab-inactive '
        "
        @click.prevent="changeTab(index, tab.id)"
      >
        {{ tab.title }}
      </li>
    </ul>
  </div>

  <div v-for="(tab, index) in tabs" :key="index">
    <component :is="tab.content" v-if="show === tab.id" v-bind="tab.props" />
  </div>
</template>

<script lang="ts" setup>
import { ref, defineEmits, onMounted } from "vue";

interface Props {
  tabLength: number;
  tabs: {
    id: number | string;
    title: string;
    content: any;
    props?: Record<string, any>;
  }[];
  fullWidth?: boolean;
  showId?: string | number;
}

const props = defineProps<Props>();

const emits = defineEmits<{
  (e: "tabChanged", value: number | string): void;
}>();

const show = ref<string | number>("");

const changeTab = (index: number, id: number | string) => {
  show.value = id;
  emits("tabChanged", id);
};

onMounted(() => {
  if (props.showId) {
    show.value = props.showId;
  } else {
    show.value = props.tabs.values[0]?.id;
  }
});
</script>

<style scoped>
.c-tab-active {
  border-bottom: 2px solid red;
}

.c-tab-list {
  margin-bottom: -2px;
}

.c-tab-inactive:hover {
  color: rgb(92, 92, 92);
}

.c-tab-grid {
  display: grid;
  align-items: center;
  grid-template-columns: repeat(v-bind(tabLength), minmax(120px, 1fr));
}
</style>
