<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Years</h2>
    </template>
    <div v-if="$page.props.flash.success" class="bg-yellow-300 text-white">
      {{ $page.props.flash.success }}
    </div>
    <div class="">
      <form @submit.prevent="submit">
        <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
          <datepicker
            v-model="form.begin"
            class="pr-2 pb-2 w-full rounded-md placeholder-indigo-300"
            label="date"
            placeholder="Enter Begin date:"
          />
          <div v-if="errors.begin">{{ errors.begin }}</div>
        </div>

        <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
          <datepicker
            v-model="form.end"
            class="pr-2 pb-2 w-full rounded-md placeholder-indigo-300"
            label="date"
            placeholder="Enter End date:"
          />
          <div v-if="errors.end">{{ errors.end }}</div>
        </div>

        <div
          class="px-4 py-2 bg-gray-100 border-t border-gray-200 flex justify-start items-center"
        >
          <button
            class="border bg-indigo-300 rounded-xl px-4 py-2 ml-4 mt-4"
            type="submit"
          >
            Create Year
          </button>
        </div>
      </form>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Label from "../../Jetstream/Label.vue";
import Datepicker from "vue3-datepicker";
import format from "date-fns/format";

export default {
  components: {
    AppLayout,
    Datepicker,
    format,
  },

  props: {
    errors: Object,
  },

  data() {
    return {
      form: this.$inertia.form({
        begin: "",
        end: "",
      }),
    };
  },

  methods: {
    submit() {
      this.form.begin = format(this.form.begin, "yyyy-MM-dd");
      this.form.end = format(this.form.end, "yyyy-MM-dd");
      this.$inertia.post(route("years.store"), this.form);
    },
  },
};
</script>
