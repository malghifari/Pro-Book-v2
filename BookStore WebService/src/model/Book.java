package model;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

public class Book {
	@SerializedName("id")
	@Expose
	private String id;
	
	@SerializedName("saleInfo")
	private SaleInfo saleInfo;
	
	@SerializedName("volumeInfo")
	@Expose
	private VolumeInfo volInfo;
	
	public String getId() {
		return id;
	}
	
	public VolumeInfo getVolumeInfo() {
		return volInfo;
	}
	
	public SaleInfo getSaleInfo() {
		return saleInfo;
	}
}
