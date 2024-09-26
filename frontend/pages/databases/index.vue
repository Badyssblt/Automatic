<script setup>

  const { $api } = useNuxtApp();

  const databases = ref([]);
  const tables = ref([]);
  const store = useAuth();

  const loadingExport = ref(false);

  const getDatabases = async () => {
    try {
      const response = await $api.get("/api/user/databases");
      databases.value = response.data;
      store.selectedDatabase = databases.value[0]
    }catch (e) {
    }
  }

  const getTables = async () => {
    try {
      const response = await $api.get('/api/user/tables', {
        params: {
          database: store.selectedDatabase,
        }
      });
      tables.value = response.data;
      store.selectedTable = tables.value[0];
    }catch (e) {

    }
  }

  const selectDatabase = (event) => {
    store.selectedDatabase = event.target.value;
    getTables();
  }

  const selectTable = (event) => {
    store.selectedTable = event.target.value;
  }
  const exportTable = async () => {
    loadingExport.value = true;
    try {
      const response = await $api.get('/api/user/export', {
        params: {
          sql: `SELECT * FROM ${store.selectedTable}`,
          type: "csv",
          database: store.selectedDatabase
        },
        responseType: 'blob'
      });

      const url = window.URL.createObjectURL(new Blob([response.data]));

      const link = document.createElement('a');
      link.href = url;

      link.setAttribute('download', `${store.selectedTable}.xlsx`);

      document.body.appendChild(link);

      link.click();

      document.body.removeChild(link);
      window.URL.revokeObjectURL(url);

    } catch (e) {
      console.error('Error during file export:', e);
    }
    loadingExport.value = false;
  };

  onMounted( async() => {
    await getDatabases();
    await getTables();
  })
</script>

<template>
  <div class="mx-auto max-w-screen-xl flex gap-6">
    <Aside>
      <AsideButton url="/databases"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
      </svg>

        Accueil</AsideButton>

      <button @click="logout">
        Se déconnecter
      </button>
    </Aside>
    <div>
      <h2 class="font-bold text-xl my-4">Base de donnée</h2>
      <div class="flex gap-4 items-end">
        <div v-if="databases" class="flex flex-col">
          <label for="databases">Toutes mes bases de données</label>
          <select name="databases" id="databases" class="px-4 py-2 rounded border border-red-600" @change="selectDatabase">
            <option :value="database" v-for="database in databases">{{database}}</option>
          </select>
        </div>
        <div v-if="tables" class="flex flex-col">
          <label for="tables">Toutes mes tables</label>
          <select name="tables" id="tables" class="px-4 py-2 rounded border border-red-600" @change="selectTable">
            <option :value="table" v-for="table in tables">{{table}}</option>
          </select>
        </div>
        <div>
          <Button @click="exportTable" :loading="loadingExport">Exporter la table</Button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>