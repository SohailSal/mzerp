<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white my-2">Create Accounts</h2>
    </template>
    <div v-if="$page.props.flash.success" class="bg-yellow-600 text-white">
      {{ $page.props.flash.success }}
    </div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
      <div class="">
        <form @submit.prevent="form.post(route('accounts.store'))">
          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 text-right w-36 font-bold"
              >Account Name :</label
            >
            <input
              type="text"
              v-model="form.name"
              class="
                pr-2
                pb-2
                w-full
                lg:w-1/4
                rounded-md
                placeholder-indigo-300
              "
              label="name"
              placeholder="Enter name:"
            />
            <div
              class="
                ml-2
                bg-red-100
                border border-red-400
                text-red-700
                px-4
                py-2
                rounded
                relative
              "
              role="alert"
              v-if="errors.name"
            >
              {{ errors.name }}
            </div>
          </div>

          <div class="p-2 mr-2 mb-2 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 text-right w-36 font-bold"
              >Account Group :</label
            >
            <treeselect
              v-model="form.group"
              max-height="150"
              :multiple="false"
              :options="option"
              :normalizer="normalizer"
              v-on:select="treeChange"
              style="max-width: 300px"
            />
            <div
              class="
                ml-2
                bg-red-100
                border border-red-400
                text-red-700
                px-4
                py-2
                rounded
                relative
              "
              role="alert"
              v-if="errors.group"
            >
              {{ errors.group }}
            </div>
          </div>
          <div
            class="
              px-4
              py-2
              bg-gray-200
              border-t border-gray-200
              flex
              justify-center
              items-center
            "
          >
            <button
              class="
                border
                rounded-xl
                shadow-md
                p-1
                px-4
                mt-1
                bg-gray-800
                text-white
                ml-2
                inline-block
                hover:bg-gray-700 hover:text-white
              "
              :disabled="form.processing"
              type="submit"
            >
              Create Account
            </button>
          </div>
        </form>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { useForm } from "@inertiajs/inertia-vue3";
import Multiselect from "@suadelabs/vue3-multiselect";
import Treeselect from "vue3-treeselect";
import "vue3-treeselect/dist/vue3-treeselect.css";

export default {
  components: {
    AppLayout,
    Multiselect,
    Treeselect,
  },

  props: {
    errors: Object,
    data: Object,
    groups: Array,
    group_first: Object,
  },

  data() {
    return {
      option: this.groups,
      normalizer(node) {
        return {
          label: node.name,
        };
      },
    };
  },
  setup(props) {
    const form = useForm({
      name: null,
      number: null,
      group: props.groups_first,
    });

    return { form };
  },

  //   data() {
  //     return {
  //       form: this.$inertia.form({
  //         name: null,
  //         number: null,
  //         group: this.group_first.id,
  //       }),
  //     };
  //   },

  //   methods: {
  //     submit() {
  //       this.$inertia.post(route("accounts.store"), this.form);
  //     },
  //   },
};
</script>
