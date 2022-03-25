<template>
  <app-layout>
    <template #header>
      <div class="grid grid-cols-2">
        <h2 class="font-semibold text-xl text-white my-2">Account Groups</h2>
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
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <jet-button @click="create" class="mt-4 ml-2">Create</jet-button>
      <jet-button @click="generate" v-if="exists" class="mt-4 ml-2"
        >Auto Generate Groups</jet-button
      >

      <!-- disabled="false" -->
      <!-- <button
      class="border bg-indigo-300 rounded-xl px-4  m-1"
      @click="check();
        this.disable = true;
        (_) => {
          setTimeout(() => {}, 1000);
        };
      "
    >
      <span>Check</span>
    </button> -->

      <input
        type="search"
        v-model="params.search"
        aria-label="Search"
        placeholder="Search..."
        class="h-9 w-full lg:w-1/4 ml-4 rounded-full placeholder-indigo-300"
      />
      <div class="">
        <table class="w-full shadow-lg border mt-4 ml-2 rounded-xl">
          <thead>
            <tr class="bg-gray-800 text-white">
              <th class="py-2 px-4 border w-2/5">Group Name</th>
              <th class="py-2 px-4 border">Group Type</th>
              <th class="py-2 px-4 border">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              class="bg-gray-100"
              v-for="item in balances.data"
              :key="item.id"
            >
              <td style="width: 30%" class="px-4 border">{{ item.name }}</td>
              <td style="width: 30%" class="px-4 border text-center">
                {{ item.type_name }}
              </td>
              <td style="width: 40%" class="px-4 border text-center">
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
                  v-if="item.delete"
                >
                  <span>Delete</span>
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
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import JetButton from "@/Jetstream/Button";
import Paginator from "@/Layouts/Paginator";
import { pickBy } from "lodash";
import { throttle } from "lodash";
import Multiselect from "@suadelabs/vue3-multiselect";

export default {
  components: {
    AppLayout,
    JetButton,
    Paginator,
    throttle,
    pickBy,
    Multiselect,
  },

  props: {
    data: Object,
    balances: Object,
    filters: Object,
    companies: Object,
    company: Object,
    exists: Object,
  },

  data() {
    return {
      co_id: this.$page.props.co_id,
      co_id: this.company,
      options: this.companies,

      params: {
        search: this.filters.search,
        // field: this.filters.field,
        // direction: this.filters.direction,
      },
    };
  },

  methods: {
    create() {
      this.$inertia.get(route("accountgroups.create"));
    },

    edit(id) {
      this.$inertia.get(route("accountgroups.edit", id));
    },

    destroy(id) {
      this.$inertia.delete(route("accountgroups.destroy", id));
    },

    generate() {
      this.$inertia.get(route("accountgroups.generate"));
    },

    coch() {
      // this.$inertia.get(route("companies.coch", this.co_id));
      this.$inertia.get(route("companies.coch", this.co_id["id"]));
    },

    sort(field) {
      this.params.field = field;
      this.params.direction = this.params.direction === "asc" ? "desc" : "asc";
    },

    check() {
      console.log("click");

      setTimeout(() => {
        console.log("timer");
        // this.postRecordSolo('clientStore/UPDATE_RECORDS_NO_TAB', this.endPoint, true)
      }, 1000);
    },

    // addRecord () {
    //   setTimeout(() => {
    //     this.postRecordSolo('clientStore/UPDATE_RECORDS_NO_TAB', this.endPoint, true)
    //   }, 1000)
    // }
  },
  watch: {
    params: {
      handler: throttle(function () {
        let params = pickBy(this.params);
        this.$inertia.get(this.route("accountgroups"), params, {
          replace: true,
          preserveState: true,
        });
      }, 150),
      deep: true,
    },
  },
};
</script>
