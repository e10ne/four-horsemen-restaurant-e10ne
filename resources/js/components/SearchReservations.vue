<template>
    <!-- prettier-ignore -->
    <div class="container border border-current bg-columnlight">
        <div class="search">
            <label>reservation id
                <input 
                    type="text" 
                    name="id" 
                    id="searchID" 
                    placeholder="reservation id" 
                    v-model="searchID" 
                    value=""
                >
            </label>
            <label>phone number
                <input 
                    type="text" 
                    name="phone" 
                    id="searchPhone" 
                    placeholder="Phone Number" 
                    v-model="searchPhone" 
                    value=""
                >
            </label>
            <label>guest name
                <input 
                    type="text" 
                    name="name" 
                    id="searchName" 
                    placeholder="Guest Name" 
                    v-model="searchName" 
                    value=""
                >
            </label>
            <label id="searchDate">date
                <input 
                    type="date" 
                    name="date" 
                    placeholder="reservation Date" 
                    v-model="searchDate" 
                    :min="IsolateDate(Date.now())"
                >
            </label>
            <label>event type
                <input 
                    type="event" 
                    name="id" 
                    id="searchEvent" 
                    placeholder="Event Type" 
                    v-model="searchEvent" 
                    value=""
                >
            </label>
            <label id="searchTime">time
                <input 
                    type="time" 
                    name="date" 
                    placeholder="reservation Time" 
                    v-model="searchTime" 
                    value=""
                >
            </label>
        </div>
        <select name="sorting" id="sorting" v-model="selectedSort">
            <option value="reservation id(ascending)">reservation id (ascending)</option>
            <option value="reservation id(descending)">reservation id (descending)</option>
            <option value="name(a-z)">name (a-z)</option>
            <option value="name(z-a)">name (z-a)</option>
            <option value="start time(ascending)">start time (ascending)</option>
            <option value="start time(descending)">start time (descending)</option>
            <option value="number of guests(ascending)">number of guests (ascending)</option>
            <option value="number of guests(descending)">number of guests (descending)</option>
        </select>
        <div class="results flex flex-row flex-shrink-0 w-10/12 flex-wrap">
            <div 
            v-for="item in computed_reservation_data" :key="item.index"
            v-on:click="SelectReservation(item)"
            class="border-black border grid grid-cols-2 grid-rows-3 h-16 p-2 text-sm reservation relative">
                <p  class="row-start-1 col-start-1 leading-none" :title="item.name">
                    {{(item.name.substring(0,15) + ((item.name.length>15)?"...":""))}}
                </p>
                <p class="row-start-2 col-start-1 leading-none">
                    {{IsolateDate(item.date_start)}} - {{IsolateTime(item.date_start)}} - {{IsolateTime(item.date_end)}}
                </p>
                <p class="row-start-3 col-start-1 leading-none">
                    {{item.guest_count}} {{ (parseInt(item.guest_count)>1) ? "guests" : "guest"}}
                </p>
                <p class="row-start-1 col-start-2 leading-none">
                    Id : {{item.id}}
                </p>
                <p class="row-start-2 col-start-2 leading-none">
                    {{item.event_type}}
                </p>
                <p class="row-start-3 col-start-2 leading-none">
                    {{getTables(item)}}
                </p>
                <div class="imgWrap row-start-1 col-start-3 col-span-2 absolute right-0 flex flex-row">
                    <img src="icons/late.svg" alt="Late" class="icon " v-if="IsLate(item.date_start,item.active)">
                    <img src="icons/event.svg" alt="Event" class="icon " v-if="item.event_type">
                </div>
            </div>
        </div>
        <form id="edit" class="edit" :class="((editState==false || editState==undefined)? 'hidden':'')"  action="/update" method="POST" v-if="!curentError">
            <input type="hidden" name="_token" :value="csrf" readonly>
            <input type="hidden" name="_method" value="PATCH" readonly>
            <input type="hidden" name="table" :value="computed_tables" readonly>
            <input type="hidden" name="id" :value="selectedID" readonly
                >
            <div id="cancel" v-on:click="toggleEdit('hide')">X</div>
            <p id="id">reservation id : {{selectedID}}</p>
            <label
                id="phone" 
            >guest phone
                <input 
                    type="text" 
                    name="phone_number" 
                    v-model="selectedPhone"
                >
           </label>
            <label
                id="name" 
            >guest name
                <input 
                    type="text" 
                    name="name" 
                    v-model="selectedName"
                >
            </label>
            <label
                id="guestCount" 
            >
                <span>guest count</span>
                <span>{{total_asigned_seats}}</span>
                <input 
                    type="number" 
                    name="guest_count" 
                    v-model="selectedGuestCount"
                >
            </label>
            <label
                id="event" 
            >event type
                <input 
                    type="text" 
                    name="event_type" 
                    v-model="selectedEvent"
                >
            </label>
            <label
                id="date" 
            >Date
                <input 
                    type="date" 
                    name="date" 
                    v-model="selectedDate"
                    :min="IsolateDate(Date.now())"
                >
            </label>
            <label
                id="notes" 
            >notes
                <br>
                <textarea 
                    type="text" 
                    name="notes" 
                    v-model="selectedNotes"
                >
                </textarea>
            </label>
            <label
                class="relative"
                id="time" 
            >Time
                <input 
                    type="time"
                    name="time"
                    v-model="selectedTime"
                >
                <div class="durationWrap">
                    <label>duration:</label>
                    <select name="endTime" id="endTime" v-model="selected_duration">
                        <option
                        v-for="value in computed_durations" :key="value"
                        :value="value"
                        >
                        {{getDurationString(value)}}
                        </option>
                    </select>
                </div>
            </label>
            <label id="tables">
                <span class="extraDataWrap"><span>Tables</span><span>{{getAvailableSeats()}}</span></span>
                <div class="availableTables">
                    <h3>Available</h3>
                    <div
                        class="custom-option"
                        v-for="table in computed_available_tables"
                        :key="table.id"
                        :value="table.id"
                        v-on:click="toggleTable(table.id)"
                        :id="`option-${table.id}`"
                    >
                        Table {{ table.id }} - {{ table.seat_count }}
                        {{ table.seat_count > 1 ? "seats" : "seat" }}
                    </div>
                </div>
                <div class="separator">
                </div>
                <div class="selectedTables">
                    <h3>selected</h3>
                    <div
                        class="custom-option"
                        v-for="table in computed_selected_tables"
                        :key="table.id"
                        :value="table.id"
                        v-on:click="toggleTable(table.id)"
                        :id="`option-${table.id}`"
                    >
                        Table {{ table.id }} - {{ table.seat_count }}
                        {{ table.seat_count > 1 ? "seats" : "seat" }}
                    </div>
                    
                </div>
            </label>
            <div class="btnWrap">
                <button type="submit" name="action" value="update" class="bg-save">
                    save
                </button>
                <button type="submit" name="action" value="cancel" class="bg-cancel">
                    cancel
                </button>
            </div>

        </form>
    </div>
</template>

<script>
export default {
    props: {
        pivot: Array,
        table_data: Array,
        reservation_data: Array,
        errors: Boolean,
    },
    data() {
        return {
            // config data
            min_duration: 60,
            max_duration: 240,
            // search data
            searchID: "",
            searchPhone: "",
            searchName: "",
            searchEvent: "",
            searchDate: undefined,
            searchTime: undefined,
            //state data
            curentError: this.errors,
            editState: false,
            // selected reservation data
            selected_reservation: undefined,
            selectedID: undefined,
            selectedPhone: undefined,
            selectedName: undefined,
            selectedEvent: undefined,
            selectedTabels: [],
            selectedGuestCount: undefined,
            selectedDate: undefined,
            selectedTime: undefined,
            selectedNotes: undefined,
            selected_duration: undefined,
            selectedSort: undefined,
            //csrf token
            csrf: document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        };
    },
    computed: {
        computed_reservation_data: function () {
            let filterd_data = this.reservation_data.filter((i) =>
                this.isValid(i)
            );
            switch (this.selectedSort) {
                case "reservation id(ascending)":
                    return filterd_data.sort((a, b) =>
                        a.id > b.id ? 1 : b.id > a.id ? -1 : 0
                    );
                case "reservation id(descending)":
                    return filterd_data.sort((a, b) =>
                        a.id > b.id ? -1 : b.id > a.id ? 1 : 0
                    );
                case "name(a-z)":
                    return filterd_data.sort((a, b) =>
                        a.name > b.name ? 1 : b.name > a.name ? -1 : 0
                    );
                case "name(z-a)":
                    return filterd_data.sort((a, b) =>
                        a.name > b.name ? -1 : b.name > a.name ? 1 : 0
                    );
                case "number of guests(ascending)":
                    return filterd_data.sort((a, b) =>
                        a.guest_count > b.guest_count
                            ? 1
                            : b.guest_count > a.guest_count
                            ? -1
                            : 0
                    );
                case "number of guests(descending)":
                    return filterd_data.sort((a, b) =>
                        a.guest_count > b.guest_count
                            ? -1
                            : b.guest_count > a.guest_count
                            ? 1
                            : 0
                    );
                case "start time(ascending)":
                    return filterd_data.sort((a, b) =>
                        a.date_start > b.date_start
                            ? 1
                            : b.date_start > a.date_start
                            ? -1
                            : 0
                    );
                case "start time(descending)":
                    return filterd_data.sort((a, b) =>
                        a.date_start > b.date_start
                            ? -1
                            : b.date_start > a.date_start
                            ? 1
                            : 0
                    );
                default:
                    return filterd_data;
            }
        },
        computed_table_data: function () {
            return this.table_data.filter((i) => this.isAvailable(i));
        },
        computed_available_tables: function () {
            return this.computed_table_data.filter((i) =>
                this.isNotSelected(i)
            );
        },
        computed_selected_tables: function () {
            return this.computed_table_data.filter((i) => this.isSelected(i));
        },
        computed_tables: function () {
            let out = "";
            out += this.selectedTabels?.join();
            return out;
        },
        table_seats: function () {
            let out = {};
            this.table_data.forEach((table) => {
                out[table.id] = table.seat_count;
            });
            return out;
        },
        total_asigned_seats: function () {
            let seats = 0;
            this.selectedTabels?.forEach((table) => {
                seats += this.table_seats[table];
            });
            return `total seats : ${seats}`;
        },
        computed_durations: function () {
            let durations = [];
            let cur = this.min_duration;
            do {
                durations.push(cur);
                cur += 15;
            } while (cur <= this.max_duration);
            return durations;
        },
    },
    methods: {
        isValid(item) {
            let name_filter = new RegExp(this.searchName, "gi");
            let event_filter = new RegExp(this.searchEvent, "gi");
            if (
                (this.searchID == item.id ||
                    this.searchID == undefined ||
                    this.searchID == "") &&
                (this.searchPhone == item.phone ||
                    this.searchPhone == undefined ||
                    this.searchPhone == "") &&
                (name_filter.test(item.name) ||
                    this.searchName == undefined ||
                    this.searchName == "") &&
                (event_filter.test(item.event_type) ||
                    this.searchEvent == undefined ||
                    this.searchEvent == "") &&
                (this.searchDate == this.IsolateDate(item.date_start) ||
                    this.searchDate == undefined ||
                    this.searchDate == "") &&
                (this.searchTime == this.IsolateTime(item.date_start) ||
                    this.searchTime == undefined ||
                    this.searchTime == "") &&
                item.canceled == false &&
                item.active == false &&
                item.done == false &&
                new Date() < new Date(item.date_end)
            )
                return true;
        },
        IsolateDate(datetime) {
            datetime = new Date(datetime);
            return `${datetime.getFullYear()}-${(
                "0" +
                (datetime.getMonth() + 1)
            ).slice(-2)}-${("0" + datetime.getDate()).slice(-2)}`;
        },
        IsolateTime(datetime) {
            datetime = new Date(datetime);
            return `${("0" + datetime.getHours()).slice(-2)}:${(
                "0" + datetime.getMinutes()
            ).slice(-2)}`;
        },
        IsLate(datetime, active) {
            if (
                parseInt(("" + Date.parse(datetime)).slice(0, -4)) <
                    parseInt(("" + Date.now()).slice(0, -4)) &&
                active == 0
            ) {
                return true;
            }
        },
        toggleEdit(state) {
            if (
                ((this.editState == true || this.editState == undefined) &&
                    state != "show") ||
                state == "hide"
            ) {
                this.editState = false;
            } else if (
                (this.editState == false && state != "hide") ||
                state == "show"
            ) {
                if (!document.getElementById("error")) {
                    console.log("reset");
                    this.curentError = false;
                }
                this.editState = true;
            }
        },
        SelectReservation(item) {
            this.selected_reservation = item;
            this.selectedID = item.id;
            this.selectedPhone = item.phone_number;
            this.selectedName = item.name;
            this.selectedEvent = item.event_type;
            this.selectedGuestCount = item.guest_count;
            this.selectedDate = this.IsolateDate(item.date_start);
            this.selectedTime = this.IsolateTime(item.date_start);
            this.selectedNotes = item.notes;
            this.selectedTabels = [];
            for (let reservations_table of this.pivot) {
                if (reservations_table.reservation_id == item.id) {
                    this.selectedTabels.push(reservations_table.table_id);
                }
            }
            this.toggleEdit("show");
        },
        toggleTable(tableID) {
            let tables = this.selectedTabels ?? [];
            if (
                typeof tableID == "number" &&
                !this.computed_tables.split(",").includes(`${tableID}`)
            ) {
                tables.push(tableID);
                document
                    .getElementById(`option-${tableID}`)
                    .classList.add("selected");
                this.selectedTabels = tables;
            } else if (
                typeof tableID == "number" &&
                this.computed_tables.split(",").includes(`${tableID}`)
            ) {
                let indexOfTable = tables.indexOf(tableID);
                delete tables[indexOfTable];
                tables = tables.filter(function (el) {
                    return el != null;
                });
                document
                    .getElementById(`option-${tableID}`)
                    .classList.remove("selected");
                this.selectedTabels = tables;
            }
        },
        isAvailable(table) {
            let id = table.id;
            let result = true;
            let startTime = new Date(this.selected_reservation?.date_start);
            let endTime = new Date(this.selected_reservation?.date_end);
            this.reservation_data.forEach((reservation) => {
                if (
                    startTime <= new Date(reservation.date_end) &&
                    endTime >= new Date(reservation.date_start)
                ) {
                    reservation.tables.forEach((reservedTable) => {
                        if (
                            reservedTable.id == id &&
                            reservation.id != this.selectedID
                        ) {
                            result = false;
                        }
                    });
                }
            });
            return result;
        },
        isNotSelected(table) {
            return !this.selectedTabels.includes(table.id);
        },
        isSelected(table) {
            return this.selectedTabels.includes(table.id);
        },
        getTables(item) {
            let out = [];
            item.tables.forEach((table) => {
                out.push(table.id);
            });
            return out.join(", ");
        },
        getDurationString(value) {
            let out = "+";
            if (Math.floor(value / 60) > 0) {
                out += ` ${Math.floor(value / 60)} hour`;
            }
            if (value % 60 > 0) {
                out += ` ${value % 60} minutes`;
            }
            return out;
        },
        getAvailableSeats() {
            let seats = 0;
            let tables = this.computed_table_data ?? [];
            tables.forEach((table) => {
                seats += table?.seat_count;
            });
            if (seats < 1) {
                return "no seats available";
            } else if (seats == 1) {
                return "1 seat available";
            } else {
                return `${seats} seats available`;
            }
        },
    },
};
</script>
