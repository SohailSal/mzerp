<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white my-2">
        Create Account Groups
      </h2>
    </template>
    <div v-if="$page.props.flash.success" class="bg-yellow-600 text-white">
      {{ $page.props.flash.success }}
    </div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
      <div class="">
        <form @submit.prevent="form.post(route('accountgroups.store'))">
          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 text-right w-36 font-bold">Name :</label>
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
              placeholder="Group name:"
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
              >Account Type :</label
            >
            <select
              v-model="form.type_id"
              @change="account_type_ch"
              class="pr-2 pb-2 w-full lg:w-1/4 rounded-md"
              label="type"
              placeholder="Enter type"
            >
              <option v-for="type in types" :key="type.id" :value="type.id">
                {{ type.name }}
              </option>
            </select>
            <div v-if="errors.type_id">{{ errors.type_id }}</div>
          </div>
          <div class="p-2 mr-2 mb-2 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 text-right w-36 font-bold"
              >Account Group :</label
            >
            <treeselect
              v-model="form.parent_id"
              max-height="150"
              :multiple="false"
              :options="data"
              :normalizer="normalizer"
              v-on:select="treeChange"
              style="max-width: 300px"
            />
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
              type="submit"
              :disabled="form.processing"
            >
              Create Account Group
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
import Treeselect from "vue3-treeselect";
import "vue3-treeselect/dist/vue3-treeselect.css";
export default {
  components: {
    AppLayout,
    Treeselect,
  },
  props: {
    errors: Object,
    types: Object,
    first: Object,
    name: String,
    data: Array,
  },

  data() {
    return {
      isError: null,
      form: this.$inertia.form({
        name: this.name,
        type_id: this.first.id,
        parent_id: null,
      }),
      options: this.data,
      normalizer(node) {
        return {
          label: node.name,
        };
      },
    };
  },
  methods: {
    treeChange(node, instanceId) {
      this.form.parent_id = node.id;
    },
    account_type_ch() {
      console.log(this.form.type_id + "------" + this.form.name);
      this.$inertia.post(route("accountgroups.create"), this.form);
    },
  },
};
</script>
