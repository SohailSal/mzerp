<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Years
        <div
          style="display: inline-block; min-width: 25%"
          class="flex-1 inline-block float-right"
        >
          <multiselect
            class="rounded-md border border-black"
            placeholder="Select Company."
            v-model="co_id"
            track-by="id"
            label="name"
            :options="options"
            @update:model-value="coch"
          >
          </multiselect>
        </div>
      </h2>
    </template>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
      <div v-if="$page.props.flash.success" class="bg-green-600 text-white">
        {{ $page.props.flash.success }}
      </div>
      <!-- <jet-button @click="create" class="mt-4 ml-8">Create</jet-button> -->

      <form @submit.prevent="form.get(route('years.create'))">
        <jet-button type="submit" @click="create" class="mt-4 ml-2"
          >Add Year</jet-button
        >

        <!-- <button
          class="border bg-indigo-300 rounded-xl px-4 py-1 m-1 ml-2 mt-4"
          type="submit"
          :disabled="form.processing"
        >
          Add Year
        </button> -->
        <!-- <select
          v-model="co_id"
          class="pr-2 ml-2 pb-2 w-full lg:w-1/4 rounded-md float-right mt-2"
          label="company"
          @change="coch"
        >
          <option v-for="type in companies" :key="type.id" :value="type.id">
            {{ type.name }}
          </option>
        </select> -->
        <!-- <div v-if="errors.type">{{ errors.type }}</div> -->
        <div class="">
          <table class="w-full shadow-lg border mt-4 ml-2 rounded-xl">
            <thead>
              <tr class="bg-indigo-100">
                <th class="py-2 px-4 border">Company</th>
                <th class="py-2 px-4 border">Begin</th>
                <th class="py-2 px-4 border">End</th>
                <th class="py-2 px-4 border">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in balances.data" :key="item.id">
                <td class="py-1 px-4 border w-2/5">
                  {{ item.company_name }}
                </td>
                <td class="py-1 px-4 border text-center">{{ item.begin }}</td>
                <td class="py-1 px-4 border text-center">{{ item.end }}</td>
                <td class="py-1 px-4 border text-center">
                  <button
                    class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
                    @click="edit(item.id)"
                    type="button"
                  >
                    <span>Edit</span>
                  </button>
                  <button
                    class="border bg-red-500 rounded-xl px-4 py-1 m-1"
                    @click="destroy(item.id)"
                    type="button"
                    v-if="item.delete"
                  >
                    <span>Delete</span>
                  </button>
                  <button
                    v-if="item.closed == 0"
                    class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
                    @click="close(item.id)"
                    type="button"
                  >
                    <span>Close Fiscal</span>
                  </button>
                </td>
              </tr>
              <tr v-if="balances.data.length === 0">
                <td class="border-t px-6 py-4" colspan="4">No Record found.</td>
              </tr>
            </tbody>
          </table>
          <paginator class="mt-6" :balances="balances" />
        </div>
      </form>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import JetButton from "@/Jetstream/Button";
import { useForm } from "@inertiajs/inertia-vue3";
import Multiselect from "@suadelabs/vue3-multiselect";
import Paginator from "@/Layouts/Paginator";
// import { Head, Link } from "@inertiajs/inertia-vue3";

export default {
  components: {
    AppLayout,
    JetButton,
    useForm,
    Multiselect,
    Paginator,
    // Link,
    // Head,
  },

  // props: ["data", "companies", "company"],
  props: {
    balances: Object,
    companies: Object,
    company: Object,
  },

  data() {
    return {
      // co_id: this.$page.props.co_id,
      co_id: this.company,
      options: this.companies,
    };
  },

  setup(props) {
    const form = useForm({});
    return { form };
  },

  methods: {
    create() {
      this.$inertia.get(route("years.create"));
    },

    edit(id) {
      this.$inertia.get(route("years.edit", id));
    },

    destroy(id) {
      this.$inertia.delete(route("years.destroy", id));
    },

    close(id) {
      this.$inertia.get(route("years.close", id));
    },

    coch() {
      // this.$inertia.get(route("companies.coch", this.co_id));
      this.$inertia.get(route("companies.coch", this.co_id["id"]));
    },
  },
};
</script>
