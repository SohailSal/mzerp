<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Company</h2>
    </template>
    <div v-if="$page.props.flash.success" class="bg-green-600 text-white">
      {{ $page.props.flash.success }}
    </div>
    <jet-button @click="create" class="mt-4 ml-8">Create</jet-button>
    <input
      type="search"
      v-model="params.search"
      aria-label="Search"
      placeholder="Search..."
      class="pr-2 pb-2 w-full lg:w-1/4 rounded-md placeholder-indigo-300"
    />
    <div class="">
      <!-- class="w-full ml-10" -->
      <table class="shadow-lg border mt-4 ml-8 rounded-xl">
        <thead>
          <tr class="bg-indigo-100">
            <th class="py-2 px-4 border">ID</th>
            <th class="py-2 px-4 border">
              <span @click="sort('name')">Name</span>
            </th>
            <th class="py-2 px-4 border">Address</th>
            <th class="py-2 px-4 border">
              <span @click="sort('email')">Email</span>
            </th>
            <th class="py-2 px-4 border">Website</th>
            <th class="py-2 px-4 border">Phone</th>
            <th class="py-2 px-4 border">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in data.data" :key="item.id">
            <td class="py-1 px-4 border">{{ item.id }}</td>
            <td class="py-1 px-4 border">{{ item.name }}</td>
            <td class="py-1 px-4 border">{{ item.address }}</td>
            <td class="py-1 px-4 border">{{ item.email }}</td>
            <td class="py-1 px-4 border">{{ item.web }}</td>
            <td class="py-1 px-4 border">{{ item.phone }}</td>
            <td class="py-1 px-4 border">
              <button
                class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
                @click="edit(item.id)"
              >
                <span>Edit</span>
              </button>
              <button
                class="border bg-red-500 rounded-xl px-4 py-1 m-1"
                @click="destroy(item.id)"
                v-if="item.delete"
              >
                <span>Delete</span>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <pagination class="mt-10" :links="data.links" />
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import JetButton from "@/Jetstream/Button";

export default {
  components: {
    AppLayout,
    JetButton,
  },

  //   props: ["data"],
  props: {
    data: Object,
  },

  data() {
    return {
      params: {
        search: null,
        field: null,
        direction: null,
      },
    };
  },

  methods: {
    create() {
      this.$inertia.get(route("companies.create"));
    },

    edit(id) {
      this.$inertia.get(route("companies.edit", id));
    },

    destroy(id) {
      this.$inertia.delete(route("companies.destroy", id));
    },

    sort(field) {
      this.params.field = field;
      this.params.direction = this.params.direction === "asc" ? "desc" : "asc";
    },
  },
  watch: {
    params: {
      handler() {
        this.$inertia.get(this.route("companies"), this.params, {
          replace: true,
          preserveState: true,
        });
      },
      deep: true,
    },
  },
};
</script>
