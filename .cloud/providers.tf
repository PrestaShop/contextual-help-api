# https://www.terraform.io/docs/providers/google/index.html
provider "google" {
  project = local.gcp_project
}

# https://www.terraform.io/docs/providers/google/provider_versions.html
provider "google-beta" {
  project = local.gcp_project
}
