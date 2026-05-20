import '@inertiajs/core';
import axios from 'axios';

declare module '@inertiajs/core' {
    export interface PageProps {
        auth: {
            user: any;
        };
    }
}

declare global {
    interface Window {
        axios: typeof axios;
        snap: any;
    }
}
