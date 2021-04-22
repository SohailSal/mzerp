<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Account Groups
      </h2>
    </template>
    <div v-if="$page.props.flash.success" class="bg-green-600 text-white">
      {{ $page.props.flash.success }}
    </div>
    <jet-button @click="create" class="mt-4 ml-8">Create</jet-button>

    <select
      v-model="co_id"
      class="pr-2 ml-2 pb-2 w-full lg:w-1/4 rounded-md"
      label="company"
      @change="coch"
    >
      <option v-for="type in companies" :key="type.id" :value="type.id">
        {{ type.name }}
      </option>
    </select>
    <div class="">
      <table class="shadow-lg border mt-4 ml-8 rounded-xl">
        <thead>
          <tr class="bg-indigo-100">
            <th class="py-2 px-4 border">Account Type</th>
            <th class="py-2 px-4 border w-2/5">Group Name</th>
            <th class="py-2 px-4 border w-2/5">Company Name</th>
            <th class="py-2 px-4 border">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in data" :key="item.id">
            <td class="py-1 px-4 border">{{ item.type_name }}</td>
            <td class="py-1 px-4 border">{{ item.name }}</td>
            <td class="py-1 px-4 border">{{ item.company_name }}</td>
            <td class="py-1 px-4 border">
              <button
                class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
                @click="edit(item.id)"
              >
                <span>Edit</span>
              </button>
              <button
                class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
                @click="destroy(item.id)"
              >
                <span>Delete</span>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
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

  props: {
    data: Object,
    companies: Object,
  },

  data() {
    return {
      co_id: this.$page.props.co_id,
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
    coch() {
      this.$inertia.get(route("companies.coch", this.co_id));
    },
  },
};
</script>
