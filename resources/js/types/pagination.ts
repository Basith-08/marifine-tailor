export type PaginationProps<T> = {
    data: T[];
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
    current_page: number;
    last_page: number;
    from: number;
    to: number; // Added
    per_page: number; // Added
    total: number;
    search: string;
}