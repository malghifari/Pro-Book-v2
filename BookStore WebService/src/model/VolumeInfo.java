package model;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

public class VolumeInfo {
	class ImageLinks{
		@SerializedName("thumbnail")
		@Expose
		private String thumbnail;
		
		public String getThumbnail() {
			return thumbnail;
		}
	}
	
	@SerializedName("title")
	@Expose
	private String title;
	
	@SerializedName("authors")
	@Expose
	private String[] authors;
	
	@SerializedName("categories")
	@Expose
	private String[] categories;
	
	@SerializedName("averageRating")
	@Expose
	private float averageRating;
	
	@SerializedName("description")
	@Expose
	private String description;
	
	@SerializedName("imageLinks")
	@Expose
	private ImageLinks imageLinks;
	
	public String getDescription() {
		return description;
	}
	
	public String getTitle() {
		return title;
	}
	
	public String[] getAuthors() {
		return authors;
	}
	
	public float getAverageRating() {
		return averageRating;
	}
	
	public String[] getCategories() {
		return categories;
	}
}
