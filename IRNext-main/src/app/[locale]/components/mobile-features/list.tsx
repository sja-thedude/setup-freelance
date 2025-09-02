import variables from '/public/assets/css/home.module.scss'

export default function Product({ workspaceDataItem }: { workspaceDataItem: any }) {
    return (
        <div className={`${variables.option} mb-3 me-3`}>
            <div className={`${variables.title}`}>{workspaceDataItem &&  workspaceDataItem?.name ? workspaceDataItem?.name : workspaceDataItem?.title}</div>
            <div className={`${variables.description}`}>{workspaceDataItem ? workspaceDataItem?.description : ''}</div>
        </div>
    );
};
