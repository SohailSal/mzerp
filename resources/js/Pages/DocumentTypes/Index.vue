<template>
  <app-layout>
    <template #header>
      <div class="grid grid-cols-2">
        <h2 class="font-semibold text-xl text-white my-2">Voucher</h2>
        <div class="justify-end">
          <multiselect
            style="width: 50%"
            class="float-right rounded-md border border-black float-right"
            placeholder="Select Company."
            v-model="co_id"
            track-by="id"
            label="name"
            :options="options"
            @update:model-value="coch"
          >
          </multiselect>
        </div>
      </div>
    </template>
    <div
      v-if="$page.props.flash.success"
      class="bg-green-600 text-white text-center"
    >
      {{ $page.props.flash.success }}
    </div>
    <div
      v-if="$page.props.flash.warning"
      class="bg-yellow-600 text-white text-center"
    >
      {{ $page.props.flash.warning }}
    </div>
    <!-- <div class=""> -->
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-2">
      <jet-button v-if="can['create']" @click="create" class="ml-2"
        >Create</jet-button
      >

      <!-- </div> -->
      <!-- <div v-if="errors.type">{{ errors.type }}</div> -->
      <div class="">
        <div class="relative overflow-x-auto mt-2 ml-2 sm:rounded-2xl">
          <table class="w-full shadow-lg border rounded-2xl">
            <thead>
              <tr class="text-white bg-gray-800">
                <th class="py-1 px-4 border">Voucher Name</th>
                <th class="py-1 px-4 border w-2/5">Prefix</th>
                <th
                  v-if="can['edit'] || can['delete']"
                  class="py-1 px-4 border"
                >
                  Actions
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                class="bg-gray-100"
                v-for="item in balances.data"
                :key="item.id"
              >
                <td style="width: 30%" class="px-4 border w-2/5">
                  {{ item.name }}
                </td>
                <td style="widht: 30%" class="px-4 border text-center">
                  {{ item.prefix }}
                </td>
                <td
                  v-if="can['edit'] || can['delete']"
                  style="widht: 40%"
                  class="px-4 border text-center"
                >
                  <button
                    class="
                      border
                      bg-indigo-300
                      rounded-xl
                      px-4
                      m-1
                      hover:text-white hover:bg-indigo-400
                    "
                    @click="edit(item.id)"
                    v-if="can['edit']"
                  >
                    <span>Edit</span>
                  </button>
                  <button
                    class="
                      border
                      bg-red-500
                      rounded-xl
                      px-4
                      m-1
                      hover:text-white hover:bg-red-600
                    "
                    @click="destroy(item.id)"
                    v-if="item.delete && can['delete']"
                  >
                    <span>Delete</span>
                  </button>
                </td>
              </tr>
              <tr v-if="balances.data.length === 0">
                <td class="border-t px-6 py-4 bg-gray-100" colspan="4">
                  No Record found.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <paginator class="mt-6" :balances="balances" />
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import JetButton from "@/Jetstream/Button";
import Multiselect from "@suadelabs/vue3-multiselect";
import Paginator from "@/Layouts/Paginator";

export default {
  components: {
    AppLayout,
    JetButton,
    Paginator,
    Multiselect,
  },

  props: ["balances", "companies", "company", "can"],

  data() {
    return {
      // co_id: this.$page.props.co_id,
      co_id: this.company,
      options: this.companies,
    };
  },

  methods: {
    create() {
      this.$inertia.get(route("documenttypes.create"));
    },

    edit(id) {
      this.$inertia.get(route("documenttypes.edit", id));
    },

    destroy(id) {
      this.$inertia.delete(route("documenttypes.destroy", id));
    },
    coch() {
      this.$inertia.get(route("companies.coch", this.co_id["id"]));
    },
  },
};
</script>
