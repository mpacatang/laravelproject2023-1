<template>
    <div :class="[{'mb-3':!noSpacing}]">
        <label v-if="!noLabel" :for="name" class="form-label">{{ label }}
            <span v-if="required" class="text-danger">*</span>
            <span v-if="hasFieldSlot('label-suffix')" class="ms-1"><slot
                name="label-suffix"></slot>
            </span>
        </label>
        <div :id="zoneId">
            <div id="dropzone-template">

                <div id="dropzone">
                    <div :style="{minHeight: height+`px`, height: height+`px`}"
                         class="dz-message needsclick dropzone d-flex align-items-center justify-content-center gap-2">
                        <div id="template-preview" :ref="'dropzoneElement'+zoneId" class="flex-grow-1">
                            <div class="fallback">
                                <input multiple name="file" type="file"/>
                            </div>
                            <div :style="{minHeight: height+`px`, height: height+`px`}"
                                 class="dz-message needsclick d-flex align-items-center flex-column justify-content-center text-center">

                                <Icon icon="cloud_upload" msr/>
                                <h5 class="mb-0 mt-1">{{ placeholder }}</h5>

                            </div>
                        </div>
                        <template v-if="showFileManager">
                            <div class="vr"></div>

                            <div :style="{height: height+'px'}"
                                 class="px-3 d-flex flex-column align-items-center justify-content-center"
                                 @click.prevent="showFileManagerModal">

                                <Icon icon="folder_open" type="msr"/>
                                <h5 class="mb-0 mt-1 text-center"> {{ $t('file_manager') }}</h5>


                            </div>
                        </template>
                    </div>
                </div>


                <div id="dz-preview-template" class="dz-preview dz-file-preview well d-none">
                    <div :id="'previewTemplate'+zoneId"></div>
                </div>

                <div id="template-previews">

                </div>

                <div id="manual-preview">
                    <Row v-if="!previewAsList">
                        <template v-for="file in addedFiles[zoneId]">
                            <Col :lg="previewGridSize" class="mt-2-5">
                                <div class="position-relative rounded text-center shadow-none border p-2">
                                    <NetworkImage :height="100" :src="file.dataURL" class="d-block mx-auto"
                                                  object-fit="contain"
                                                  rounded show-full-image/>
                                    <a class="text-muted fw-bold mt-1-5 font-13 max-lines">{{
                                            file.name ?? "Uploaded Image"
                                        }} </a>
                                    <p class="mb-0" v-html="this.filesize(file.size??0)"/>
                                    <a class="d-flex align-items-center justify-content-center text-muted
                             position-absolute top-0 start-100 translate-middle rounded-circle border shadow bg-light"
                                       href="" style="padding: 3px; "
                                       @click.prevent="removeFile(file)">
                                        <Icon icon="close" size="13"/>
                                    </a>
                                </div>
                            </Col>
                        </template>
                    </Row>
                    <div v-else :class="[{'mt-2': (addedFiles[zoneId] && addedFiles[zoneId].length>0)}]"
                         class="d-flex flex-column gap-2">
                        <div v-for="file in addedFiles[zoneId]">
                            <div class="border rounded p-2 d-flex align-items-center">
                                <NetworkImage :height="40" :src="file.dataURL" object-fit="contain" rounded
                                              show-full-image/>
                                <div class="flex-grow-1 ms-2  text-truncate">
                                    <p class="text-muted fw-bold mb-0">{{
                                            file.name ?? "Uploaded Image"
                                        }} </p>
                                    <p class="mb-0" v-html="this.filesize(file.size??0)"/>
                                </div>

                                <span
                                    class="border d-flex align-items-center justify-content-center text-muted rounded-circle cursor-pointer ms-1"
                                    style="padding: 3px;"
                                    @click.prevent="removeFile(file)">
                            <Icon icon="close" size="xxs"/>
                        </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <FormValidationError :errors="errors" :name="name"></FormValidationError>
    </div>
    <div>


        <template v-if="showFileManager">
            <VModal v-model="show_modal" xl>
                <Card id="file-manager" class="mb-0">
                    <div class="d-flex">
                        <div class="w-100">
                            <div>
                                <ol class="breadcrumb m-0 p-2-5 ps-3">
                                    <li
                                        v-for="(item, index) in getItems"
                                        :key="index"
                                        :class="{ active: item.active }"
                                        class="breadcrumb-item cursor-pointer"
                                        @click="goToInnerFolder(item.file)"
                                    >
                                        <template v-if="item.active">{{ item.text }}</template>
                                        <span v-else class="fw-medium">{{ item.text }}</span>
                                    </li>
                                </ol>
                            </div>
                            <hr class="dashed p-0 m-0">

                            <PageLoading :loading="file_manager_loading">

                                <SimpleBar id="leftside-menu-container" style="height: 75vh">

                                    <CardBody>
                                        <div v-if="getFolders.length>0" :class="[{'mb-3': getFiles.length>0}]">
                                            <p class="text-muted fw-semibold">{{ $t('folders') }}</p>
                                            <Row :row-cols="3" gap="2-5">
                                                <Col v-for="folder in getFolders">
                                                    <div
                                                        class="border p-2-5 folder-card rounded d-flex align-items-center"
                                                        @click="goToInnerFolder(folder)">
                                                        <Icon color="yellowish-orange" fill icon="folder" size="28"/>
                                                        <span class="ms-2 fw-medium max-lines"> {{
                                                                getUppercase(folder.name)
                                                            }}</span>
                                                    </div>
                                                </Col>
                                            </Row>
                                        </div>
                                        <template v-if="getFiles.length>0">
                                            <p class="text-muted fw-semibold">{{ $t('files') }}</p>
                                            <div class="d-flex gap-3 flex-wrap">
                                                <div v-for="file in getFiles">
                                                    <div class="border p-2-5 file-card rounded d-flex"
                                                         @click="onSelectFile(file)">
                                                        <NetworkImage :src="file.url" rounded size="144" object-fit="contain"/>
                                                        <span class="mt-2 fw-medium max-lines"> {{
                                                                getUppercase(file.name)
                                                            }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </CardBody>

                                </SimpleBar>

                            </PageLoading>
                        </div>


                    </div>
                </Card>
            </VModal>
        </template>
    </div>
</template>

<script lang="ts">
import Dropzone from "dropzone";
import {defineComponent, PropType} from "vue";
import Col from "@js/components/Col.vue";
import Icon from "@js/components/Icon.vue";
import NetworkImage from "@js/components/NetworkImage.vue";
import VModal from "@js/components/VModal.vue";
import CardHeader from "@js/components/CardHeader.vue";
import PageLoading from "@js/components/PageLoading.vue";
import VItem from "@js/components/VItem.vue";
import Row from "@js/components/Row.vue";
import CardBody from "@js/components/CardBody.vue";
import CreateButton from "@js/components/buttons/AddNewButton.vue";
import EditActionButton from "@js/components/buttons/EditActionButton.vue";
import Button from "@js/components/buttons/Button.vue";
import Card from "@js/components/Card.vue";
import Table from "@js/components/Table.vue";
import PageHeader from "@js/components/PageHeader.vue";
import Layout from "@js/pages/admin/layouts/Layout.vue";
import {IFile} from "@js/types/models/file_manager";
import {adminAPI} from "@js/services/api/request_url";
import {base64} from "@js/shared/utils";
import Request from "@js/services/api/request";
import {handleException} from "@js/services/api/handle_exception";
import {SimpleBar} from 'simplebar-vue3';
import UtilMixin from "@js/shared/mixins/UtilMixin";
import FormValidationError from "@js/components/form/FormValidationError.vue";
import {FileService} from "@js/services/file_service";

Dropzone.autoDiscover = false;
export default defineComponent({
    components: {
        FormValidationError,
        VModal, NetworkImage, Icon, Col, CardHeader,
        PageLoading,
        VItem,
        Row,
        SimpleBar,

        CardBody,
        CreateButton,
        EditActionButton,
        Button,
        Card,
        Table,
        PageHeader,
        Layout,
    },
    mixins: [UtilMixin],
    props: {
        name: {
            type: String
        },
        placeholder: {
            type: String,
            default: 'Drop files here or click to upload.'
        },
        zoneId: {
            type: String,
            default: 'zone'
        },
        height: {
            type: [Number, String],
            default: 150
        },
        options: {
            type: Object,
            default: {}
        },
        acceptedFiles: {
            type: String,
            default: FileService.types.images
        },
        label: {
            type: String
        },
        required: {
            type: Boolean,
            default: false
        },
        noLabel: {
            type: Boolean,
            default: false
        },
        noSpacing: {
            type: Boolean,
            default: false,
        },
        topSpacing: {
            type: Boolean,
            default: false,
        },
        defaultFiles: {
            type: Object,
            default: []
        },
        maxFiles: {
            type: Number,
            default: 10
        },
        onAdded: {
            type: Function,
        },
        onRemoved: {
            type: Function,
        },
        validator: Function as PropType<(file) => boolean>,
        previewAsList: {
            type: Boolean,
            default: false
        },
        previewGridSize: {
            type: [Number, String],
            default: 3
        },
        showFileManager: {
            type: Boolean,
            default: false
        },
        errors: null,

    },
    data() {
        return {
            wasQueueAutoProcess: true,
            files: 0,
            dropzone: null,
            addedFiles: {},
            previewTemplate: null,
            show_modal: false,

            managers_files: [] as IFile[],
            file_manager_path: '',
            file_manager_loading: true,
            selected_folder: null as IFile,
            previous_folder: null as IFile,

        };
    },
    computed: {

        getFiles() {
            return this.managers_files.filter((f) => f.type == 'file');
        },
        getFolders() {
            return this.managers_files.filter((f) => f.type == 'folder');
        },
        getItems() {
            let list: {
                text: string,
                active?: boolean,
                file?: IFile
            }[] = [
                {
                    text: this.$t('storage'),
                },
            ];

            if (this.selected_folder) {
                list.push({
                        text: this.getUppercase(this.selected_folder.name),
                        active: true,
                    },
                )
            }
            return list;
        }
    },
    watch: {
        defaultFiles(newVal, oldVal) {
            this.addDefaultFile(newVal);
        },

    },
    created() {

    },

    beforeDestroy() {
        this.dropzone.destroy();
    },
    methods: {
        hasFieldSlot(fieldName) {
            return typeof this.$slots[fieldName] !== 'undefined'
        },
        showFileManagerModal() {
            this.show_modal = true;
        },
        addDefaultFile(files: any) {
            if (files instanceof Array) {
                //TODO: ---- Old Imp
                // this.addedFiles = [...newVal];
                if (this.addedFiles[this.zoneId] == null) {
                    this.addedFiles[this.zoneId] = [];
                }
                this.addedFiles[this.zoneId].push(...files);

            } else if (files instanceof Object) {
                if (this.addedFiles[this.zoneId] == null) {
                    this.addedFiles[this.zoneId] = [];
                }
                this.addedFiles[this.zoneId].push(files);
            }
        },
        removeAllFiles(){
            this.dropzone.removeAllFiles();
        },
        filesize: function (size) {
            return this.dropzone.filesize(size);
        },
        accept: function (file, done) {
            return this.dropzone.accept(file, done);
        },
        removeFile: function (file) {
            this.dropzone.removeFile(file);
        },


        //====================== File Managers
        getUppercase(name: string) {
            name = name.replaceAll("_", " ");
            return name.charAt(0).toUpperCase() + name.slice(1);
        },

        goToInnerFolder(file?: IFile) {
            if (file == null || file.type == 'folder') {
                this.previous_folder = this.selected_folder;
                this.selected_folder = file;
                this.getFileManager(file)
            }
        },
        onSelectFile(file: IFile) {
            const self = this;

            fetch(file.url)
                .then(resp => resp.blob())
                .then(blob => {
                    let dFile = new File([blob], file.name, blob);
                    self.dropzone.addFile(dFile);
                });
            this.show_modal = false;
        },
        closeInformation() {
            this.show_file_information = null;
        },
        onDownload(file: IFile) {
            function downloadImage(url, name) {
                fetch(url)
                    .then(resp => resp.blob())
                    .then(blob => {
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.style.display = 'none';
                        a.href = url;
                        // the filename you want
                        a.download = name;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                    })
                    .catch(() => alert('An error sorry'));
            }

            downloadImage(file.url, file.name);
        },
        checkForDuplicate(file) {
            const fid = file.upload?.uuid;
            if (fid == null)
                return false;
            if (this.addedFiles[this.zoneId] != null) {
                for (const uFile of this.addedFiles[this.zoneId]) {
                    if (file.upload?.uuid != null && fid == file.upload?.uuid) {
                        return true;
                    }
                }
            }
        },

        async getFileManager(file?: IFile) {
            if (this.showFileManager) {
                this.file_manager_loading = true;
                try {
                    let url = adminAPI.file_manager.get;
                    if (file != null) {
                        url += "?path=" + base64.encode(file.path);
                    }
                    const response = await Request.getAuth<IFile>(url);
                    this.managers_files = response.data;
                } catch (error) {
                    handleException(error);
                } finally {
                    this.file_manager_loading = false;
                }
            }
        },
        onAddFile(file) {
            const self = this;
            if (self.addedFiles[self.zoneId] && (self.addedFiles[self.zoneId].length > self.maxFiles - 1)) {
                // self.removeFile(self.addedFiles[self.zoneId][0]);
            }
            if (self.addedFiles[self.zoneId] == null || self.maxFiles==1    ) {
                self.addedFiles[self.zoneId] = [];
            }
            self.addedFiles[self.zoneId].push(file)

            if (self.onAdded != null) {
                self.onAdded(file);
            }
            self.$emit("file-added", file);
        },
        onThumbnailCreated(uFile) {

            const files = this.addedFiles[this.zoneId];
            if (files != null) {
                this.addedFiles[this.zoneId] = files.map((file) => {
                    if (file.upload!=null && uFile.upload!=null && file.upload.uuid == uFile.upload.uuid) {
                        return uFile;
                    }
                    return file;
                })
            }

            //     for (let i = 0; i < files.length; i++) {
            //         if (files[i].upload.uuid == uFile.upload.uuid) {
            //             console.info(files[i].upload.uuid);
            //             console.info(uFile.upload.uuid);
            //             files[i] = uFile;
            //         }
            //     }
            // }
            // for (const file of files) {
            //
            // }
            // this.addedFiles[this.zoneId] = deepCopy(files);
        }

    },
    mounted() {
        const self = this;
        let previewTemplate = document.querySelector<HTMLElement>("#" + this.zoneId + ' #dz-preview-template');
        this.dropzone = new Dropzone(
            this.$refs['dropzoneElement' + this.zoneId],
            {
                url: '/',
                previewTemplate: previewTemplate?.innerHTML,
                previewsContainer: "#" + this.zoneId + " #template-preview",
                thumbnailWidth: 2000,
                thumbnailHeight: 2000,
                resizeQuality: 1,
                autoProcessQueue: false,
                acceptedFiles: this.acceptedFiles,
                thumbnailMethod: 'contain',
                maxFiles: this.maxFiles,

            }
        );

        this.getFileManager();

        let vm = this;
        this.dropzone.on("thumbnail", function (file, dataUrl) {
            vm.onThumbnailCreated(file);
        });
        this.dropzone.on("addedfile", function (file) {

            if (self.validator) {
                if (!self.validator(file)) {
                    return;
                }
            }
            vm.onAddFile(file);
            if (vm.onAdded != null) {
                vm.onAdded(file);
            }
            vm.$emit("files-added", file);

        })

        this.dropzone.on("removedfile", function (file) {
            console.info(file);
            self.addedFiles[self.zoneId] = self.addedFiles[self.zoneId].filter(function (localFile) {
                return file.dataURL !== localFile.dataURL;
            });
            if (vm.onRemoved != null) {
                vm.onRemoved(file);
            }
            //TODO: ----- Remove
            vm.$emit("file-removed", file);
        });

        if (this.defaultFiles != null) {
            this.addDefaultFile(this.defaultFiles);
        }
    },

});
</script>

<style>

/*#dropzone-template img:hover {*/
/*    border-radius: 0.5px !important;*/
/*    transform: scale(20);*/
/*    z-index: 99999;*/
/*    position: absolute;*/
/*    transition: 0.3s all ease-in-out;*/
/*    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);*/

/*}*/
</style>
