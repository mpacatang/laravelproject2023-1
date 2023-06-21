<template>
    <div :class="[{'p-3': !table_layout.as_card}]" class="v-table">

        <div v-if="showTopAction">
            <div :class="[{'pt-3 px-3': table_layout.as_card}]"
                 class="mb-3 gap-2-5 d-flex align-items-center flex-wrap">
                <div v-if="hasSlot('pre-actions')">
                    <slot name="pre-actions"></slot>
                </div>
                <div :class="[{'item-disabled': loading}]">
                    <form class="app-search" v-on:submit.prevent="onSearchChange">
                        <div class="app-search-box dropdown">
                            <input v-model="searchQuery" :placeholder="$t('search')"
                                   class="form-control border bg-transparent" name="search"
                                   @change="onSearchChange"/>

                            <Icon class="search-icon" color="muted" icon="search" size="xs"></Icon>
                            <Icon :class="[{'d-none': !show_search_cancel}]" class="search-widget-icon-close"
                                  color="muted" enterkeyhint="done" icon="cancel" size="xs"
                                  @click="onSearchCancel"></Icon>
                        </div>
                    </form>
                </div>
                <div v-if="!loading">
                    <div class="d-flex">
                        <Button v-if="hasRefresh" class="px-1-5 me-2-5" color="purple" flexed-icon variant="soft"
                                @click="onRefresh">
                            <Icon class="" icon="cached"></Icon>
                        </Button>
                        <div v-if="hasColumnCustomizer" class="dropdown me-2-5">
                            <a aria-expanded="false" aria-haspopup="false"
                               class="nav-link dropdown-toggle arrow-none"
                               data-bs-auto-close="outside" data-bs-toggle="dropdown" href="#" role="button">
                                <Button class="px-1-5" color="teal" flexed-icon variant="soft">
                                    <Icon icon="table_chart"></Icon>
                                </Button>
                            </a>
                            <div class="dropdown-menu dropdown-menu-animated" style="width: 220px">
                                <div class="py-1 px-2-5">
                                    <FormCheckbox v-model="showAllColumn" :label="$t('show_all')" name="reset"
                                                  no-spacing @change="onShowAllColumn">
                                    </FormCheckbox>
                                </div>
                                <hr class="dashed">
                                <div v-for="column in shownColumn" class="py-1 px-2-5">
                                    <FormCheckbox v-model="column.show"
                                                  :label="column.label" :name="column.field+column.label" no-spacing>

                                    </FormCheckbox>
                                </div>
                            </div>
                        </div>

                        <div v-if="hasExportOption" class="dropdown">
                            <a aria-expanded="false" aria-haspopup="false"
                               class="nav-link dropdown-toggle arrow-none"
                               data-bs-toggle="dropdown" href="#" role="button">
                                <Button class="" color="rose" flexed-icon variant="soft">
                                    <Icon class="me-1" icon="download"></Icon>
                                    {{ $t('export') }}
                                </Button>
                            </a>
                            <div class="dropdown-menu dropdown-menu-animated" style="width: 220px">
                                <div v-if="hasPrintOption" class="dropdown-item cursor-pointer" @click="onPrint">
                                    <Icon class="me-1" icon="print"></Icon>
                                    {{ $t('print') }}
                                </div>
                                <template v-if="hasDownloadOption">
                                    <hr class="dashed">
                                    <div class="mx-3 mb-1">
                                        <span class="text-muted fw-semibold font-13">{{ $t('download') }}</span>
                                    </div>
                                    <div v-if="hasExcelOption" class="dropdown-item cursor-pointer"
                                         @click="onDownloadExcel">
                                        <Icon class="me-1" icon="table"></Icon>
                                        {{ $t('Excel') }}
                                    </div>
                                    <div v-if="hasCSVOption" class="dropdown-item cursor-pointer"
                                         @click="onDownloadCSV">
                                        <Icon class="me-1" icon="article"></Icon>
                                        {{ $t('CSV') }}
                                    </div>
                                    <div v-if="hasPDFOption" class="dropdown-item cursor-pointer"
                                         @click="onDownloadPDF">
                                        <Icon class="me-1" icon="picture_as_pdf"></Icon>
                                        {{ $t('PDF') }}
                                    </div>
                                </template>

                            </div>
                        </div>

                    </div>
                </div>
                <div v-if="hasSlot('custom-filter')">
                    <slot name="custom-filter"></slot>
                </div>
                <div v-if="hasSlot('table-actions')" class="ms-auto">
                    <slot name="table-actions"></slot>
                </div>

            </div>
        </div>


        <PageLoading id="printable_area" :loading="loading">

            <div class="table-responsive">
                <table :class="[{'table-bordered': table_layout.bordered}, [{'text-center': table_layout.center}]]"
                       class="table table-nowrap mb-0 table-hover">
                    <thead class="table-light">
                    <tr>
                        <th v-for="(column, index) in getColumns()" :key="column.field"
                            :class="[{'cursor-pointer': column.sort}, {'first-data': index===0}, {'d-print-none':column.printNone}]"
                            :style="[{'width': column.width!=null?column.width+`px`:null}]" class="table-th"
                            @click="handleSort(column)">
                            <span class="table-th-content fw-semibold font-15">{{ column.label }}</span>
                            <button v-if="column.sort" :class="['table-sort-'+this.getSortClassFromHeader(column)]"
                                    class="ms-1 table-sort"/>
                        </th>
                    </tr>
                    </thead>


                    <tbody>
                    <tr v-for="(row, index) in paginationData">
                        <td v-for="(column, cindex) in getColumns()" :key="column.field + '-' + index"
                            :class="[{'first-data': cindex===0},  {'d-print-none':column.printNone}, getLabelClass(column)]"
                            style="vertical-align: middle">
                            <span
                                :class="[{'cursor-pointer':column.onClick!=null}, {'cursor-copy':column.onCopy!=null}]"
                                @click="clickOnColumn(column, row)">
                                <template v-if="isFieldSlot(column.field)">
                                    <slot :name="column.field"
                                          v-bind="{row: row, index: index, field: column.field, value: getCellData(row,column)}"></slot>
                                </template>
                                <template v-else>
                                    <span :class="[{'text-primary':column.onClick!=null}]" class="font-14">{{
                                            getCellData(row, column)
                                        }}</span>
                                </template>
                            </span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div v-if="hasData" :class="[{'p-3': table_layout.as_card}]"
                 class="row justify-content-between align-items-center pt-3 d-print-none">
                <div class="col">
                    <label class="form-label mb-0">Show
                        <select class="mx-1 form-select form-select-sm w-auto d-inline-block"
                                @change="onEntryChange($event)">
                            <option v-for="entry in getEntries" :key="entry.label" :selected="entry.size===this.size"
                                    :value="entry.size">
                                {{ entry.label }}
                            </option>
                        </select> entries</label>
                </div>
                <div class="col text-center">
                    <p class="fw-medium mb-0">Showing {{ showingPageStart }} to {{ showingPageEnd }} of <span
                        class="fw-bold">{{
                            this.filteredData.length
                        }}</span>
                        entries</p>
                </div>
                <div class="col text-end">
                    <ul class="pagination pagination-square justify-content-end mb-0">
                        <li :class="[{'disabled': !hasPrevious}]" class="page-item">
                            <a aria-label="Previous" class="page-link" style="cursor:pointer;" @click="previousPage">
                                <!--                                <span aria-hidden="true">«</span>-->
                                <span aria-hidden="true">{{ $t('previous') }}</span>
                            </a>
                        </li>
                        <li v-for="page in maxPage" :class="[{'active': page===currentPage}]" class="page-item">
                            <a class="page-link" style="cursor:pointer;" @click="changePage(page)">{{ page }}</a>
                        </li>
                        <li :class="[{'disabled': !hasNext}]" class="page-item">
                            <a aria-label="Next" class="page-link" style="cursor:pointer;" @click="nextPage">
                                <!--                                <span aria-hidden="true">»</span>-->
                                <span aria-hidden="true">{{ $t('next') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div v-else class="my-3">
                <p class="text-center fw-medium">{{ getPlaceholder }}
                    <span v-if="canClearFilter" class="text-primary ms-1 cursor-pointer"
                          @click="clearFilter">{{ $t('clear_filter') }}</span>
                </p>
            </div>


        </PageLoading>
    </div>
</template>

<script lang="ts">

import Icon from "./Icon.vue";
import {defineComponent, PropType} from "vue";
import Loading from "@js/components/Loading.vue";
import {ITableColumn, ITableEntry, ITableOption} from "@js/types/models/models";
import {ITableLayoutOption, useLayoutStore} from "@js/services/state/states";
import FormInput from "@js/components/form/FormInput.vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import PageLoading from "@js/components/PageLoading.vue";
import Button from "@js/components/buttons/Button.vue";
import Col from "@js/components/Col.vue";
import Row from "@js/components/Row.vue";
import FormSwitch from "@js/components/form/FormSwitch.vue";
import FormCheckbox from "@js/components/form/FormCheckbox.vue";
import {FileService} from "@js/services/file_service";
import {ToastNotification} from "@js/services/toast_notification";
import NavigatorService from "@js/services/navigator_service";


export default defineComponent({
    components: {FormCheckbox, FormSwitch, Row, Col, Button, PageLoading, FormInput, Loading, Icon},
    mixins: [UtilMixin],
    props: {
        data: {
            type: Array
        },
        loading: {
            type: Boolean,
            default: false
        },
        option: {
            type: Object as PropType<ITableOption>,
            required: true,
        },


    },
    watch: {
        data: function () {
            this.sortedData = this.data;
            this.recalculate();
        },
        searchQuery(newVal: string) {
            this.show_search_cancel = newVal != null && newVal?.length != 0;
        },
        shownColumn: {
            handler() {
                this.showAllColumn = this.shownColumn.filter(function (sCol) {
                    return !sCol.show;
                }).length == 0;
            },
            deep: true
        }
    },
    data() {
        return {
            currentPage: 1,
            maxPage: 4,
            filteredData: [],
            sortedData: [],
            paginationData: [],
            size: 10,
            searchQuery: '',
            shownColumn: [] as {
                label: string,
                field: string,
                show: boolean
            }[],
            showAllColumn: true,
            sort: {
                type: 'neutral' as 'neutral' | 'asc' | 'desc',
                field: null as string
            },
            show_search_cancel: false,
            table_layout: {
                as_card: false,
                bordered: false,
                center: false
            } as ITableLayoutOption
        }
    },
    mounted() {
        this.sortedData = this.data;
        this.recalculate();
        this.shownColumn = this.option.columns.map(function (col) {
            return {
                field: col.field,
                label: col.label,
                show: col.show ?? true
            }
        });
        let layoutStore = useLayoutStore();
        this.table_layout = layoutStore.getTableLayout();
        const self = this;
        layoutStore.$subscribe((mut, state) => {
            self.table_layout = state.layout.table_layout;
        });
    },
    methods: {
        onSearchChange() {
            this.currentPage = 1;
            this.recalculate();
        },
        previousPage() {
            this.changePage(this.currentPage - 1);
        },
        changePage(page: number) {
            if (page > 0 && page <= this.maxPage) {
                this.currentPage = page;
                this.recalculate();
            }
        },
        nextPage() {
            this.changePage(this.currentPage + 1);
        },
        onEntryChange(entry) {
            this.size = entry.target.value;
            this.currentPage = 1;
            this.recalculate();
        },
        recalculate() {
            this.filteredData = this.getSearchResults(this.sortedData);
            if (this.size == -1) {
                this.maxPage = 1;
                this.paginationData = this.filteredData;
            } else {
                this.maxPage = Math.ceil(this.filteredData.length / this.size);
                this.paginationData = this.filteredData.slice((this.currentPage - 1) * this.size, (this.currentPage) * this.size);
            }

            // this.filteredData = this.data.slice((this.currentPage - 1) * this.size, (this.currentPage) * this.size);
        },
        getSearchResults(data: []) {
            const filterList: any[] = [];
            if (data == null)
                return [];
            if (this.searchQuery.length === 0) {
                return data;
            } else {
                const self = this;
                data.forEach(function (row: any) {
                    for (let i = 0; i < self.option.columns.length; i++) {
                        const singleData = row[self.option.columns[i].field];
                        if (singleData && singleData.toString().toLowerCase().includes(self.searchQuery.toLowerCase())) {
                            filterList.push(row);
                            break;
                        }
                    }

                })
            }
            return filterList;
        },
        getCellData(row, column: ITableColumn) {
            if (column.data != null) {
                return column.data(row);
            }
            return row[column.field];
        },
        handleSort(column: ITableColumn) {
            if (!column.sort)
                return;


            let cType = this.sort.field === column.field ? this.sort.type : 'neutral';
            this.sort.field = column.field;
            if (cType === "neutral" || cType === 'desc') {
                this.sort.type = 'asc';
            } else if (cType === "asc") {
                this.sort.type = 'desc';
            }
            let type = this.sort.type;
            let sort = column.sort;

            const self = this;
            this.sortedData = this.data.sort(function (first, second) {
                let result: number;
                if (typeof sort === "boolean") {
                    let firstR = self.getCellData(first, column);
                    let secondR = self.getCellData(second, column);
                    if (typeof firstR === 'number') {
                        result = self.getCellData(first, column) - self.getCellData(second, column)
                    } else {
                        result = firstR.toString().localeCompare(secondR.toString());
                    }
                } else {
                    result = sort.compare(first, second);
                }
                return type == 'asc' ? -result : result;
            })
            this.changePage(1);
            this.recalculate();

        },
        isFieldSlot(fieldName) {
            return typeof this.$slots[fieldName] !== 'undefined'
        },
        getSortClassFromHeader(column: ITableColumn) {
            return column.field === this.sort.field ? this.sort.type : 'neutral';
        },
        onSearchCancel() {
            this.searchQuery = '';
            this.recalculate();
        },
        getLabelClass(column: ITableColumn) {
            if (column.labelStyle != null && column.labelStyle.fontWeight != null) {
                return "fw-" + column.labelStyle.fontWeight;
            }
        },
        onRefresh() {
            if (this.option.onRefresh)
                this.option.onRefresh();
        },
        getColumns() {
            const self = this;
            return this.option.columns.filter(function (col) {
                return self.shownColumn.find(function (sCol) {
                    return col.field == sCol.field && sCol.show
                }) != null;
            })
        },
        onShowAllColumn() {
            this.showAllColumn = true;
            this.shownColumn = this.shownColumn.map(function (sCol) {
                return {...sCol, show: true};
            })
        },
        clearFilter() {
            this.onSearchCancel();
        },
        async clickOnColumn(column: ITableColumn, row) {
            if (column.onClick != null) {
                column.onClick(row);
            } else if (column.onCopy != null) {
                let text = column.onCopy(row);
                console.info(text);
                const success = await NavigatorService.copyToClipboard(text);
                if (success) {
                    ToastNotification.show({
                        message: '"' + text + '" copied',
                        short: true,
                        hide_progress: true
                    });
                }
            }
        },
        onPrint() {
            if (this.hasAllExportEnable || (this.hasPrintOption && this.option.exports.print.auto)) {
                try {

                    let html = '<HTML lang="en">\n<HEAD>\n<title></title>';
                    if (document.getElementsByTagName != null) {
                        const headTags = document.getElementsByTagName("head");
                        if (headTags.length > 0) html += headTags[0].innerHTML;

                        const footerTags = document.getElementsByTagName("footer-script");
                        if (footerTags.length > 0) html += footerTags[0].innerHTML;

                    }

                    html += "\n</HEAD>\n<BODY style='pointer-events: none'>\n";
                    const printReadyElem = document.getElementById('printable_area');

                    if (printReadyElem != null) html += printReadyElem.innerHTML;
                    else {
                        alert("Error, no contents.");
                        return;
                    }

                    html += "\n</BODY>\n</HTML>";
                    const printWin = window.open("", "print");
                    printWin.document.open();
                    printWin.document.write(html);
                    printWin.document.close();

                    printWin.print();
                    printWin.onafterprint = (function () {
                        printWin.close();
                    })

                } catch (e) {
                    console.log("Here");
                }
            } else if (this.hasPrintOption && this.option.exports.print.callback != null) {
                this.option.exports.print.callback(this.paginationData);
            }
        },
        onDownloadExcel() {
            if (this.paginationData.length == 0) {
                ToastNotification.error(this.$t('there_is_no_any_data'));
                return;
            }
            if (this.option.exports?.excel?.callback != null)
                this.option.exports?.excel?.callback(this.paginationData);
            else if ((this.hasAllExportEnable || this.option.exports?.excel?.auto) && this.option.exports?.autoData != null) {
                let info = this.option.exports.autoData(this.paginationData);
                FileService.exportExcel(info.data, info.fileName);
            }
        },
        onDownloadCSV() {
            if (this.paginationData.length == 0) {
                ToastNotification.error(this.$t('there_is_no_any_data'));
                return;
            }
            if (this.option.exports?.csv?.callback != null)
                this.option.exports?.csv?.callback(this.paginationData);
            else if ((this.hasAllExportEnable || this.option.exports?.excel?.auto) && this.option.exports?.autoData != null) {
                let info = this.option.exports.autoData(this.paginationData);
                FileService.exportCSV(info.data, info.fileName);
            }
        },
        onDownloadPDF() {
            if (this.paginationData.length == 0) {
                ToastNotification.error(this.$t('there_is_no_any_data'));
                return;
            }
            if (this.option.exports?.pdf?.callback != null)
                this.option.exports?.pdf?.callback(this.paginationData);
            else if ((this.hasAllExportEnable || this.option.exports?.pdf?.auto) && this.option.exports?.autoData != null) {
                let info = this.option.exports.autoData(this.paginationData);
                FileService.exportPDF(info.data, info.fileName);
            }
        },
        hasSlot(slotName) {
            return typeof this.$slots[slotName] !== 'undefined'
        },

    },
    computed: {
        canClearFilter() {
            return this.searchQuery != null && this.searchQuery.length > 0;
        },
        showingPageStart() {
            return (this.currentPage - 1) * this.size + 1;
        },
        showingPageEnd() {
            if (this.size == -1) {
                return this.filteredData.length;
            }
            return Math.min((this.currentPage) * this.size, this.filteredData.length);
        },
        hasPrevious() {
            return this.currentPage > 1;
        },
        hasNext() {
            return this.currentPage !== this.maxPage;
        },
        hasData() {
            return this.filteredData != null && this.filteredData.length > 0;
        },
        getPlaceholder() {
            return this.option?.placeholder?.message ?? this.$t('there_is_no_data')
        },
        hasAllExportEnable() {
            return this.option.exports.enableAll;
        },
        hasExportOption() {
            return this.option.exports != null;
        },
        hasDownloadOption() {
            return this.hasAllExportEnable || (this.hasExportOption && (this.hasPDFOption || this.hasExcelOption || this.hasCSVOption))
        },
        hasPrintOption() {
            return this.hasAllExportEnable || (this.hasExportOption && (this.option.exports?.print))
        },
        hasPDFOption() {
            return this.hasAllExportEnable || (this.hasExportOption && (this.option.exports?.pdf?.enable || this.option.exports?.pdf?.auto))
        },
        hasExcelOption() {
            return this.hasAllExportEnable || (this.hasExportOption && (this.option.exports?.excel?.enable || this.option.exports?.excel?.auto))
        },
        hasCSVOption() {
            return this.hasAllExportEnable || (this.hasExportOption && (this.option.exports?.csv?.enable || this.option.exports?.csv?.auto))
        },
        hasRefresh() {
            return this.option.onRefresh;
        },
        hasColumnCustomizer() {
            return this.option.columnCustomizer;
        },
        getEntries(): ITableEntry[] {
            return this.option.entries ?? [
                {
                    label: 10,
                    size: 10
                },
                {
                    label: 20,
                    size: 20
                },
                {
                    label: 50,
                    size: 50
                },
                {
                    label: "All",
                    size: -1
                },

            ]
        },
        showTopAction() {
            return !(this.option.removeTopAction ?? false);
        },


    }
});
</script>

<style scoped>
.data-hide {
    transition: opacity 0s ease-in-out;
    opacity: 0;
    height: 0;
}

.data-show {
    transition: opacity 0.5s ease-in;
    opacity: 1;
}

.first-data {
    padding: 1rem 2.5rem;
}

.search-widget-icon-close {
    position: absolute;
    z-index: 10;
    font-size: 18px;
    line-height: 42px;

    top: 50%;
    color: #8c98a5;
    transform: translateY(-50%);
    cursor: pointer;
    right: 14px;
    left: auto !important;
}


</style>
