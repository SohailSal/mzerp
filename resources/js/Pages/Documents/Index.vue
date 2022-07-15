<template>
  <app-layout>
    <template #header>
      <div class="grid grid-cols-2">
        <h2 class="font-semibold text-xl text-white my-2">Transactions</h2>
        <div class="justify-end">
          <select
            v-model="yr_id"
            class="
              pr-2
              ml-2
              pb-2
              text-gray-700
              w-full
              lg:w-5/12
              rounded-md
              float-right
            "
            label="year"
            @change="yrch"
          >
            <option v-for="type in years" :key="type.id" :value="type.id">
              {{ type.name }}
            </option>
          </select>
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
    <div
      v-if="$page.props.flash.error"
      class="bg-red-600 text-white text-center"
    >
      {{ $page.props.flash.error }}
    </div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-2">
      <!-- <div class="p-2 mr-2 mb-2 ml-2 flex flex-wrap"> -->
      <jet-button
        v-if="yearclosed && can['create']"
        @click="create"
        class="ml-2 float-left"
        >Create</jet-button
      >
      <input
        type="text"
        class="
          ml-4
          h-8
          px-2
          w-80
          border-gray-800
          ring-gray-800 ring-1
          outline-none
        "
        @change="search_data"
        v-model="params.search"
        aria-label="Search"
        placeholder="Search..."
      />
      <button
        @click="search_data"
        class="
          border-2
          pb-2.5
          pt-1
          bg-gray-800
          border-gray-800
          px-1
          hover:bg-gray-700
        "
      >
        <svg
          class="w-8 h-4 text-white"
          fill="currentColor"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 25 20"
        >
          <path
            d="M16.32 14.9l5.39 5.4a1 1 0 0 1-1.42 1.4l-5.38-5.38a8 8 0 1 1 1.41-1.41zM10 16a6 6 0 1 0 0-12 6 6 0 0 0 0 12z"
          />
        </svg>
      </button>
      <div v-if="can['create']" class="mt-2">
        <form @submit.prevent="submit">
          <input
            class="ml-4 border-gray-800 w-1/4 ring-gray-800 ring-1 outline-none"
            type="file"
            placeholder="Upload Excel Sheet"
            title="Upload Excel Sheet"
            v-on:change="onFileChange"
            accept=".xlsx"
          />
          <div
            class="
              ml-2
              bg-red-100
              border border-red-400
              text-red-700
              px-4
              py-1
              rounded
              inline-block
            "
            role="alert"
            v-if="errors.file"
          >
            {{ errors.file }}
          </div>
          <button
            class="
              inline-flex
              items-center
              px-4
              py-2
              bg-gray-800
              border border-transparent
              rounded-md
              font-bold
              text-xs text-white
              uppercase
              tracking-widest
              hover:bg-gray-700
              active:bg-gray-900
              focus:outline-none focus:border-gray-900 focus:shadow-outline-gray
              transition
              ease-in-out
              duration-150
              ml-2
            "
            type="submit"
          >
            Upload Sales Sheet
          </button>
          <a
            class="
              inline-flex
              items-center
              px-4
              py-2
              bg-gray-800
              border border-transparent
              rounded-md
              font-bold
              text-xs text-white
              uppercase
              tracking-widest
              hover:bg-gray-700
              active:bg-gray-900
              focus:outline-none focus:border-gray-900 focus:shadow-outline-gray
              transition
              ease-in-out
              duration-150
              ml-2
            "
            type="button"
            :href="'documents/downloadFile'"
          >
            <!-- @click="downloadFormat" -->
            Download Sales Format
          </a>
          <a
            class="
              inline-flex
              items-center
              px-4
              py-2
              bg-gray-800
              border border-transparent
              rounded-md
              font-bold
              text-xs text-white
              uppercase
              tracking-widest
              hover:bg-gray-700
              active:bg-gray-900
              focus:outline-none focus:border-gray-900 focus:shadow-outline-gray
              transition
              ease-in-out
              duration-150
              ml-2
            "
            type="button"
            target="_blank"
            :href="'documents/Accountpdf'"
          >
            <!-- @click="downloadFormat" -->
            Download Accounts
          </a>
        </form>
      </div>
      <!-- class="pr-2 ml-2 pb-2 w-full lg:w-1/4 rounded-md float-right" -->

      <!-- class="pr-2 ml-2 pb-2 w-full lg:w-1/4 rounded-md" -->

      <!--   <div v-if="errors.type">{{ errors.type }}</div> -->

      <!-- <div v-if="errors.type">{{ errors.type }}</div> -->
      <!-- </div> -->
      <!-- <div class="w-full px-8"> -->
      <!-- ml-8 mr-8 -->
      <div class="relative overflow-x-auto mt-2 ml-2 sm:rounded-2xl">
        <table class="w-full shadow-lg border rounded-2xl">
          <thead>
            <tr class="text-white bg-gray-800">
              <th class="py-1 px-4 border">Reference</th>
              <th class="py-1 px-4 border">Date</th>
              <th class="py-1 px-4 border w-2/5">Description</th>
              <th class="py-1 px-4 border">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr class="bg-gray-100" v-for="item in data.data" :key="item.id">
              <td style="width: 15%" class="px-4 border text-center">
                {{ item.ref }}
              </td>
              <td style="width: 15%" class="px-4 border text-center">
                {{ item.date }}
              </td>
              <td style="width: 40%" class="px-4 border w-2/5">
                {{ item.description }}
              </td>
              <!-- v-if="can['edit'] || can['delete']" -->
              <td style="width: 30%" class="px-4 border text-center">
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
                  v-if="yearclosed && can['edit']"
                >
                  <span>Edit</span>
                </button>
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
                  v-else
                >
                  <span>Show</span>
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
                <div
                  class="
                    border
                    bg-gray-300
                    text-md
                    rounded-full
                    shadow-md
                    px-4
                    inline-block
                    hover:bg-gray-700 hover:text-white
                  "
                >
                  <a :href="'pd/' + item.id" target="_blank">Voucher in PDF</a>
                </div>
              </td>
            </tr>
            <tr v-if="data.data.length === 0">
              <td class="border-t px-6 py-4 bg-gray-100" colspan="4">
                No Record found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <paginator class="mt-6" :balances="data" />
      <!-- </div> -->
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import JetButton from "@/Jetstream/Button";
import Paginator from "@/Layouts/Paginator";
import moment from "moment";
import { pickBy } from "lodash";
import { throttle } from "lodash";
import Multiselect from "@suadelabs/vue3-multiselect";
import { useForm } from "@inertiajs/inertia-vue3";

export default {
  components: {
    AppLayout,
    JetButton,
    Paginator,
    throttle,
    pickBy,
    moment,
    Multiselect,
    useForm,
  },

  props: {
    errors: Object,
    data: Object,
    filters: Object,
    companies: Object,
    company: Object,
    years: Object,
    yearclosed: Object,
    can: Object,
  },

  setup() {
    const form = useForm({
      avatar: null,
    });
    return { form };
  },
  data() {
    return {
      // co_id: this.$page.props.co_id,
      co_id: this.company,
      options: this.companies,
      yr_id: this.$page.props.yr_id,

      params: {
        search: this.filters.search,
        field: this.filters.field,
        direction: this.filters.direction,
      },
    };
  },

  methods: {
    //   for file upload
    submit() {
      if (this.form.avatar) {
        this.form.post(route("sales.trial.read"));
      } else {
        alert("Please select file first");
      }
    },
    downloadFormat() {
      this.$inertia.get(route("documents.downloadFile"));
    },

    onFileChange(e) {
      var files = e.target.files || e.dataTransfer.files;
      if (!files.length) return;
      this.form.avatar = files[0];
    },
    // file upload end
    create() {
      this.$inertia.get(route("documents.create"));
    },

    edit(id) {
      this.$inertia.get(route("documents.edit", id));
    },

    destroy(id) {
      this.$inertia.delete(route("documents.destroy", id));
    },

    coch() {
      this.$inertia.get(route("companies.coch", this.co_id["id"]));
    },
    // yrch() {
    //   this.$inertia.get(route("companies.yrch", this.yr_id));
    // },
    yrch() {
      this.$inertia.get(route("years.yrch", this.yr_id));
    },

    sort(field) {
      this.params.field = field;
      this.params.direction = this.params.direction === "asc" ? "desc" : "asc";
    },
    search_data() {
      let params = pickBy(this.params);
      this.$inertia.get(this.route("documents"), params, {
        replace: true,
        preserveState: true,
      });
    },
  },
  watch: {
    params: {
      handler: throttle(function () {
        let params = pickBy(this.params);
        if (params.search == null) {
          this.$inertia.get(this.route("documents"), params, {
            replace: true,
            preserveState: true,
          });
        }
      }, 150),
      deep: true,
    },
  },
};
</script>
