data "google_secret_manager_secret" "contextual_help_api_key" {
  provider = google-beta
  secret_id   = "contextual-help-api-key"
}

resource "google_secret_manager_secret_iam_member" "contextual_help_api_key" {
  secret_id = data.google_secret_manager_secret.contextual_help_api_key.id
  role      = "roles/secretmanager.secretAccessor"
  member    = "serviceAccount:${data.google_project.project.number}-compute@developer.gserviceaccount.com"
}

data "google_secret_manager_secret" "contextual_help_api_google_analytics" {
  provider = google-beta
  secret_id   = "contextual-help-api-google-analytics"
}

resource "google_secret_manager_secret_iam_member" "contextual_help_api_google_analytics" {
  secret_id = data.google_secret_manager_secret.contextual_help_api_google_analytics.id
  role      = "roles/secretmanager.secretAccessor"
  member    = "serviceAccount:${data.google_project.project.number}-compute@developer.gserviceaccount.com"
}
