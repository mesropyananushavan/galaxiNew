#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

STATUS_FILE="shared/PROJECT_STATUS.json"
PROGRESS_FILE="docs/progress-log.md"

if [[ ! -f "$STATUS_FILE" ]]; then
  echo "Missing $STATUS_FILE" >&2
  exit 1
fi

if [[ ! -f "$PROGRESS_FILE" ]]; then
  echo "Missing $PROGRESS_FILE" >&2
  exit 1
fi

python3 -m json.tool "$STATUS_FILE" >/dev/null

tracked_changes="$(git status --short -- "$PROGRESS_FILE" "$STATUS_FILE")"

if [[ -z "$tracked_changes" ]]; then
  echo "Checkpoint docs are clean: $PROGRESS_FILE + $STATUS_FILE"
  exit 0
fi

echo "Checkpoint docs changed:" >&2
echo "$tracked_changes" >&2

echo >&2
echo "Next step: include both files in the same checkpoint commit when code or QA state changes." >&2
exit 2
