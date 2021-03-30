<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Voucher</h2>
    </template>
    <div class="">
      <form @submit.prevent="submit">
        <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
          <input
            type="text"
            v-model="form.name"
            class="pr-2 pb-2 w-full lg:w-1/4 rounded-md"
            label="name"
          />
          <div v-if="errors.name">{{ errors.name }}</div>
        </div>
        <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
          <input
            type="text"
            v-model="form.prefix"
            class="pr-2 pb-2 w-full lg:w-1/4 rounded-md"
            label="prefix"
          />
          <div v-if="errors.prefix">{{ errors.prefix }}</div>
        </div>
        <!-- <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
          <select
            v-model="form.type"
            class="pr-2 pb-2 w-full lg:w-1/4 rounded-md"
            label="type"
            placeholder="Enter type"
          >
            <option v-for="type in types" :key="type.id" :value="type.id">
              {{ type.name }}
            </option>
          </select>
          <div v-if="errors.type">{{ errors.type }}</div>
        </div> -->
        <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
          <select
            v-model="form.company"
            class="pr-2 pb-2 w-full lg:w-1/4 rounded-md"
            label="company"
            placeholder="Enter Company"
          >
            <option v-for="type in companies" :key="type.id" :value="type.id">
              {{ type.name }}
            </option>
          </select>
          <div v-if="errors.type">{{ errors.type }}</div>
        </div>
        <div
          class="px-4 py-2 bg-gray-100 border-t border-gray-200 flex justify-start items-center"
        >
          <button
            class="border bg-indigo-300 rounded-xl px-4 py-2 ml-4 mt-4"
            type="submit"
          >
            Update Voucher
          </button>
        </div>
      </form>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";

export default {
  components: {
    AppLayout,
  },

  props: {
    errors: Object,
    documenttype: Object,
    // types: Object,
    companies: Object,
  },

  data() {
    return {
      form: this.$inertia.form({
        name: this.documenttype.name,
        prefix: this.documenttype.prefix,
        // type: this.accountgroup.type_id,
        company: this.documenttype.company_id,
      }),
    };
  },

  methods: {
    submit() {
      this.$inertia.put(
        route("documenttypes.update", this.documenttype.id),
        this.form
      );
    },
  },
};
</script>
