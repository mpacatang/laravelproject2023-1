<template>
    <Layout id="file-manager">
        <PageHeader :items="breadcrumb_items" :title="$t('file_manager')"/>

        <Card class="overflow-hidden">
            <div class="d-flex">
                <div class="w-100">
                    <div class="d-flex align-items-center">
                        <ol class="flex-grow-1 breadcrumb m-0 p-2-5 ps-3">
                            <li
                                v-for="(item, index) in getItems"
                                :key="index"
                                :class="{ active: item.active }"
                                class="breadcrumb-item cursor-pointer d-flex align-items-center"
                                @click="goToInnerFolder(item.file)"
                            >
                                <Icon :icon="item.root?'cloud':'folder_open'" class="me-1-5" size="18"></Icon>
                                <template v-if="item.active">{{ item.text }}</template>
                                <span v-else class="fw-medium">{{ item.text }}</span>
                            </li>
                        </ol>
                        <Button class="me-2" color="bluish-purple" flexed-icon variant="soft"
                                @click="showUploadFileModal">
                            <Icon class="me-1-5" icon="cloud_upload" size="18"></Icon>
                            {{ $t('upload_images') }}
                        </Button>
                    </div>
                    <hr class="dashed p-0 m-0">

                    <PageLoading :loading="page_loading">

                        <SimpleBar id="leftside-menu-container" style="height: 75vh">

                            <CardBody>
                                <div v-if="getFolders.length>0" :class="[{'mb-3': getFiles.length>0}]">
                                    <p class="text-muted fw-semibold">{{ $t('folders') }}</p>
                                    <Row :row-cols="show_file_information?4:5" gap="2-5">
                                        <Col v-for="folder in getFolders">
                                            <div class="border p-2-5 folder-card rounded"
                                                 @click="goToInnerFolder(folder)">
                                                <Icon color="yellowish-orange" fill icon="folder" size="28"/>
                                                <span class="ms-2 fw-medium"> {{ getUppercase(folder.name) }}</span>
                                            </div>
                                        </Col>
                                    </Row>
                                </div>
                                <template v-if="getFiles.length>0">
                                    <p class="text-muted fw-semibold">{{ $t('files') }}</p>
                                    <div class="d-flex gap-3 flex-wrap">
                                        <div v-for="file in getFiles">
                                            <div class="border p-2-5 file-card rounded" @click="showInformation(file)">
                                                <NetworkImage :src="file.url" rounded size="144"/>
                                                <span class="mt-2 fw-medium"> {{ getUppercase(file.name) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </CardBody>

                        </SimpleBar>

                    </PageLoading>
                </div>

                <div :class="[{'show': show_file_information}]" class="file-info">
                    <template v-if="show_file_information">
                        <div class=" p-2-5 d-flex align-items-center">
                            <Icon icon="info" size="20"></Icon>
                            <span class="ms-2 fw-medium flex-grow-1">{{ $t('information') }}</span>
                            <Icon class="cursor-pointer" icon="close" size="20" @click="closeInformation"></Icon>
                        </div>

                        <hr class="dashed p-0 m-0">

                        <CardBody>
                            <div class="text-center">
                                <NetworkImage :src="show_file_information.url" height="250" rounded show-full-image></NetworkImage>
                            </div>
                            <div class="mt-3 d-flex align-content-center">
                                <div class="flex-grow-1">
                                    <p class="fw-medium mb-0">
                                        {{ getUppercase(show_file_information.name) }}
                                    </p>
                                    <p class="text-muted mb-0">
                                        {{ show_file_information.path }}
                                    </p>
                                </div>
                                <Button class="p-1-5" color="primary" variant="soft"
                                        @click="onDownload(show_file_information)">
                                    <Icon icon="file_download"></Icon>
                                </Button>
                            </div>
                        </CardBody>
                    </template>
                </div>

            </div>
        </Card>


        <VModal v-model="show_upload_modal" close-btn>
            <Card class="mb-0">
                <CardHeader :title="$t('upload_zip')" icon="folder_zip"></CardHeader>
                <CardBody>
                    <p><span class="fw-medium text-danger">{{ $t('note') }}:</span>
                        <span class="ms-1">{{ $t('only_upload_zip_with_contains_images') }}</span></p>
                    <FileUpload :accepted-files="zip_file_type" :errors="errors"
                                :max-files="1"
                                :placeholder="$t('only_zip_allowed')"
                                name="zip"
                                no-label
                                ref="zip_dropzone"
                                preview-as-list
                                :validator="fileValidator"
                                v-on:file-added="zip_helper.onFileUpload"
                                v-on:file-removed="zip_helper.onFileRemoved"/>

                    <div class="text-end">
                        <LoadingButton flexed-icon icon="cloud_upload" @click="uploadZip">
                            {{ $t('upload_zip') }}
                        </LoadingButton>
                    </div>
                </CardBody>
            </Card>
        </VModal>
    </Layout>
</template>

<script lang="ts">

import PageHeader from "@js/components/PageHeader.vue";
import Layout from "@js/pages/admin/layouts/Layout.vue";
import Request from "@js/services/api/request";
import {adminAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {IBreadcrumb} from "@js/types/models/models";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IFile} from "@js/types/models/file_manager";
import {base64} from "@js/shared/utils";
import {SimpleBar} from 'simplebar-vue3';
import VModal from "@js/components/VModal.vue";
import {Components} from "@js/components/components";
import {FileService} from "@js/services/file_service";
import {FileUploadHelper} from "@js/types/models/file_upload_helper";
import ValidationErrorMixin from "@js/shared/mixins/ValidationErrorMixin";
import {ToastNotification} from "@js/services/toast_notification";


export default defineComponent({
    components: {
        VModal,
        SimpleBar,
        PageHeader,
        ...Components,
        Layout,
    },
    mixins: [UtilMixin, ValidationErrorMixin],
    data() {

        const zip_file_type = FileService.types.zip;
        const uploaded_folder_location = {
            name: "uploaded_files",
            path: "public/uploaded_files",
            storage_path: "uploaded_files",
            type: "folder", url: "/storage/uploaded_files",
            id: 0
        };

        return {
            zip_file_type,
            uploaded_folder_location,
            files: [] as IFile[],
            path: this.$route.params.path === '' ? null : this.$route.params.path,
            page_loading: true,
            selected_folder: null as IFile,
            previous_folder: null as IFile,
            show_file_information: null as IFile,
            show_upload_modal: false,
            zip_helper: new FileUploadHelper({max: 1}),
        }
    },
    computed: {
      breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('file_manager'),
                    active: true,
                },
            ];
        },
        getFiles() {
            return this.files.filter((f) => f.type == 'file');
        },
        getFolders() {
            return this.files.filter((f) => f.type == 'folder');
        },
        getItems() {
            let list: {
                text: string,
                active?: boolean,
                root?: boolean
                file?: IFile
            }[] = [
                {
                    text: this.$t('storage'),
                    root: true
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
        },

    },

    methods: {
        fileValidator(file): boolean {
            this.clearAllError();

            if(!FileService.verifyFileType(file, FileService.extensions.zip)) {
                this.addError('zip', "Please upload zip only");
                return false;
            }
            return true;
        },

        getUppercase(name: string) {
            name = name.replaceAll("_", " ");
            return name.charAt(0).toUpperCase() + name.slice(1);
        },
        showUploadFileModal() {
            this.show_upload_modal = true;
        },
        goToInnerFolder(file?: IFile) {
            if (file == null || file.type == 'folder') {
                this.previous_folder = this.selected_folder;
                this.selected_folder = file;
                this.getFileManager(file)
            }
        },
        showInformation(file: IFile) {
            this.show_file_information = file;
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
        async getFileManager(file?: IFile) {
            this.page_loading = true;
            try {
                let url = adminAPI.file_manager.get;
                if (file != null) {
                    url += "?path=" + base64.encode(file.path);
                }
                const response = await Request.getAuth<IFile>(url, {forced: true});
                this.files = response.data;
            } catch (error) {
                handleException(error);
            } finally {
                this.page_loading = false;
            }
        },
        async uploadZip() {
            const zip = this.zip_helper.getFile();

            if (zip == null) {
                this.addError('zip', this.$t('please_upload_zip_file'));
                return;
            }

            const fd = new FormData();
            fd.append('fileName', 'fileName');
            fd.append('file', zip);
            fd.append('mimeType', 'application/zip');

            try {
                let url = adminAPI.file_manager.upload;
                const response = await Request.postAuth(url, fd);
                if (response.success()) {
                    this.goToInnerFolder(this.uploaded_folder_location)
                    ToastNotification.success(this.$t('extracted'));
                    this.show_upload_modal = false;
                    this.$refs.zip_dropzone.removeAllFiles();
                }
            } catch (error) {
                handleException(error);
            } finally {

            }
            this.page_loading = false;

        }
    },
    mounted() {
        this.setTitle(this.$t('file_manager'));
        this.getFileManager();
    }
});

</script>
