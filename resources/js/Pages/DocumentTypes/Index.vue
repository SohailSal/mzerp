<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Voucher</h2>
    </template>
    <div v-if="$page.props.flash.success" class="bg-green-600 text-white">
      {{ $page.props.flash.success }}
    </div>
    <jet-button @click="create" class="mt-4 ml-8">Create</jet-button>
    <div class="">
      <table class="shadow-lg border mt-4 ml-8 rounded-xl">
        <thead>
          <tr class="bg-indigo-100">
            <th class="py-2 px-4 border">Voucher Name</th>
            <th class="py-2 px-4 border w-2/5">Prefix</th>
            <th class="py-2 px-4 border">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in data" :key="item.id">
            <td class="py-1 px-4 border w-2/5">{{ item.name }}</td>
            <td class="py-1 px-4 border">{{ item.prefix }}</td>
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

  props: ["data"],

  data() {
    return {};
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
  },
};
</script>
