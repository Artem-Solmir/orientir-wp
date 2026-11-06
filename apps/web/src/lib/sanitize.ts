// One sanitizer for every write path: the ETL and the Agent API share the same allow-list.
export { sanitize } from "../../scripts/etl/content";
