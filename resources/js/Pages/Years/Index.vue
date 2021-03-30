<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Years</h2>
    </template>
    <div v-if="$page.props.flash.success" class="bg-green-600 text-white">
      {{ $page.props.flash.success }}
    </div>
    <jet-button @click="create" class="mt-4 ml-8">Create</jet-button>
    <div class="">
      <table class="shadow-lg border mt-4 ml-8 rounded-xl">
        <thead>
          <tr class="bg-indigo-100">
            <th class="py-2 px-4 border">Company</th>
            <th class="py-2 px-4 border">Begin</th>
            <th class="py-2 px-4 border">End</th>
            <th class="py-2 px-4 border">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in data" :key="item.id">
            <!-- <td class="py-1 px-4 border w-2/5">{{ item.company_id }}</td> -->
            <td class="py-1 px-4 border w-2/5">{{ item.company_name }}</td>
            <td class="py-1 px-4 border">{{ item.begin }}</td>
            <td class="py-1 px-4 border">{{ item.end }}</td>
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
      this.$inertia.get(route("years.create"));
    },

    edit(id) {
      this.$inertia.get(route("years.edit", id));
    },

    destroy(id) {
      this.$inertia.delete(route("years.destroy", id));
    },
  },
};
</script>
