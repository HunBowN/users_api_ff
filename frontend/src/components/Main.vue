<script>

export default {
  name: 'Main',
  data() {
    return {
      loading: false,
      logBtnDisabled: false,
      timerBtnDisabled: false,
      fetchUsersBtnDisabled: false,
      log: null,
      errors: [],
      seconds: 0,
      timer: null,
      products: null,
      relatives: [],
      relativesCount: 0,
      limit: 15,
      columns: [
        { field: 'lastname', header: 'Lastname' },
        { field: 'city', header: 'City' },
        { field: 'count', header: 'Count' },
      ],
      sidebarVisible: false,


    }
  },
  async mounted() {
    await this.getRelatives(0);
    await this.getRelativesCount();
  },
  methods: {
    async getRelatives(offset = 0) {
      this.loading = true;
      try {
        let response = await fetch('/get_relatives?' + new URLSearchParams({
          limit: this.limit,
          offset: offset,
        }));
        let data = await response.json();
        this.relatives = data.relatives;
      } catch (error) {
        this.errors.push(error)
        console.log(error);
      } finally {
        this.loading = false
      }

    },
    goToPage(e) {
      let offset = this.limit * e.page
      this.getRelatives(offset);

    },
    async getRelativesCount() {
      try {
        let response = await fetch('/get_relatives_count');
        let data = await response.json();
        this.relativesCount = data.relativesCount.count;
      } catch (error) {
        this.errors.push(error)
        console.log(error);
      }
    },
    async fetchUsers() {
      this.loading = true;
      this.logBtnDisabled = true;
      this.fetchUsersBtnDisabled = true;
      this.errors = [];

      try {
        let response = await fetch('/get_users');

        let data = await response.json();

        if (data.error) {
          if (data.code == 429) {
            this.startTimer(301)
            throw new Error('429, слишком много запросов, попробуйте позже');
          }
          throw new Error(data.message)
        }

      } catch (error) {
        this.errors.push(error)
        console.log(error);
      } finally {
        this.loading = false
        this.logBtnDisabled = false
        this.fetchUsersBtnDisabled = false
        this.showSidebar()
      }

    },
    async getLog() {
      let res = await fetch('/storage/logs/get_users.log');
      let text = await res.text();
      this.log = text;
    },
    startTimer(seconds) {
      if (this.timer) {
        clearInterval(this.timer);
      }
      this.seconds = seconds
      this.timerBtnDisabled = true;
      this.timer = setInterval(() => {
        if (this.seconds > 0) {
          this.seconds--;
        }
        if (this.seconds == 0) {
          this.stopTimer();
        }
      }, 1000)
    },

    stopTimer() {
      this.timerBtnDisabled = false;
      clearInterval(this.timer);
    },

    async showSidebar() {
      this.sidebarVisible = true;
      await this.getLog();
      this.logScrollBottom();
    },

    logScrollBottom() {
      this.$refs.endOfLog.scrollIntoView({ behavior: 'smooth' })
    }
  }
}
</script>

<template>
  <div class="page-header">
    <h1>UsersAPI</h1>
    <ProgressSpinner class="loader" fill="var(--surface-ground)" v-if="loading" />
  </div>

  <div class="error__wrapper">
    <Message severity="error" v-if="errors.length" v-for="error in errors">{{ error }}</Message>
  </div>

  <div v-if="seconds" class="timer">{{ seconds }}</div>

  <Button v-tooltip="{ value: 'Fetch from API and insert into table 20k users', showDelay: 300, hideDelay: 300 }"
    :loading="loading" :disabled="fetchUsersBtnDisabled" outlined label="Fetch users" @click="fetchUsers()">
  </Button>

  <Button outlined @click="showSidebar()">Show log</Button>

  <Button outlined v-if="seconds" @click="stopTimer()">Stop timer</Button>

  <DataTable :value="relatives" tableStyle="min-width: 50rem">
    <Column v-for="col of columns" :key="col.field" :field="col.field" :header="col.header"></Column>
  </DataTable>
  <Paginator :rows="limit" :totalRecords="relativesCount" @page="goToPage($event)"></Paginator>

  <Sidebar v-model:visible="sidebarVisible" header="Process log" position="right" class="w-4">
    <Button @click="logScrollBottom()">Go end</Button>
    <p v-html="log" ref="log"></p>
    <span ref="endOfLog"></span>
  </Sidebar>

</template>


<style scoped>
.page-header {
  display: flex;
  justify-content: start;
  align-items: center;
}

.loader {
  width: 50px;
  height: 50px;
  margin-right: auto;
  margin-left: 15px;
}

.error__wrapper {
  width: 400px;
}
</style>
