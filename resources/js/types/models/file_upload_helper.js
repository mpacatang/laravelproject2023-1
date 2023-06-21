import Request from "../../services/api/request";
export class FileUploadHelper {
    max = 1;
    files = [];
    defaultFiles = [];
    constructor(params) {
        this.max = params?.max ?? 10;
        this.files = [];
    }
    addDefaultFile(params) {
        if (params.url != null) {
            if (this.max === 1) {
                this.defaultFiles = [];
            }
            this.defaultFiles.push({
                dataURL: params.url, uploaded: true, id: params.id
            });
        }
    }
    getFiles() {
        return this.files;
    }
    getFile() {
        return this.files.length > 0 ? this.files[0] : null;
    }
    getImageDataFile() {
        return Request.getImageData(this.getFile()?.dataURL);
    }
    getImageDataFiles() {
        if (this.files == null)
            return null;
        const imageList = [];
        for (const file of this.files) {
            imageList.push(Request.getImageData(file.dataURL));
        }
        return imageList;
    }
    onFileUpload = (file) => {
        if (this.max === 1) {
            this.files = [];
        }
        this.files.push(file);
    };
    onFileAdded = this.onFileUpload;
    onFileRemoved = (file) => {
        if (this.max === 1) {
            this.files = [];
        }
        else {
            //TODO: ----- Need to change
            // this.files.
        }
    };
    removeFile = (file) => {
        this.files = this.files.filter(function (item) {
            return item !== file;
        });
        this.defaultFiles = this.defaultFiles.filter(function (item) {
            return item !== file;
        });
    };
    removeAllFile = () => {
        this.files = [];
    };
    shiftAllImageToUploaded() {
        console.log(this.defaultFiles);
        for (const file of this.files) {
            this.defaultFiles.push({ ...file, uploaded: true });
        }
        this.files = [];
    }
    getAllFiles() {
        return [...this.files, this.defaultFiles];
    }
}
